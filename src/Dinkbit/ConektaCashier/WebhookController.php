<?php

namespace Dinkbit\ConektaCashier;

use Conekta;
use Conekta_Event;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use yii\web\Controller;

class WebhookController extends Controller
{
    /**
     * Handle a Conekta webhook call.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleWebhook()
    {
        $payload = $this->getJsonPayload();

        if (!$this->eventExistsOnConekta($payload['id'])) {
            return;
        }

        $method = 'handle'.studly_case(str_replace('.', '_', $payload['type']));

        if (method_exists($this, $method)) {
            return $this->{$method}($payload);
        } else {
            return $this->missingMethod();
        }
    }

    /**
     * Verify with Stripe that the event is genuine.
     *
     * @param string $id
     *
     * @return bool
     */
    protected function eventExistsOnConekta($id)
    {
        try {
            Conekta::setApiKey(\Yii::$app->params['services.conekta.secret']);

            return !is_null(Conekta_Event::where(['id' => $id]));
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Handle a failed payment from a Conekta subscription.
     *
     * @param array $payload
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleSubscriptionPaymentFailed(array $payload)
    {
        $billable = $this->getBillable($payload['data']['object']['customer_id']);

        if ($billable) {
            $billable->subscription()->cancel();
        }

        return new Response('Webhook Handled', 200);
    }

    /**
     * Get the billable entity instance by Conekta ID.
     *
     * @param string $conektaId
     *
     * @return \Dinkbit\ConektaCashier\BillableInterface
     */
    protected function getBillable($conektaId)
    {
        return App::make('Dinkbit\ConektaCashier\BillableRepositoryInterface')->find($conektaId);
    }

    /**
     * Get the JSON payload for the request.
     *
     * @return array
     */
    protected function getJsonPayload()
    {
        return (array) json_decode(Request::getContent(), true);
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param array $parameters
     *
     * @return mixed
     */
    public function missingMethod($parameters = [])
    {
        return new Response();
    }
}
