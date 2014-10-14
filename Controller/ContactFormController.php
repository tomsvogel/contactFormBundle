<?php

namespace Arkulpa\Bundle\ContactFormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function securityTokenAction()
    {
        $token = $this->get('form.csrf_provider')->generateCsrfToken('pool_device');
        return $this->render('ArkulpaContactFormBundle:Default:token.html.twig', array('token' => $token));
    }

    public function sendAction(Request $request)
    {
        try {
            $form = $this->get('form.factory')->create(new PoolDeviceValidateType());
            $validationErrors = $this->get('arkulpa.contact.form.validator')->bindAndValidate($form, $request);
            if ($validationErrors !== null) {
                return $this->generateValidationErrorResponse($validationErrors);
            }
        } catch (\Exception $e) {
            return $this->generateLogicErrorResponse($e);
        }
        return $this->generateSuccesResponse();

    }


    protected function generateSuccesResponse($data = null)
    {
        $returnValue = array('status' => 'success', 'data' => $data);
        return new Response(json_encode($returnValue));
    }

    protected function  generateValidationErrorResponse($validationErrors)
    {
        $returnValue = array('status' => 'fail', 'data' => array('errors' => $validationErrors));
        return new Response(json_encode($returnValue), 400);
    }

    protected function generateLogicErrorResponse($e)
    {
        if ($e instanceof \Exception) {
            $translatedError = $this->get('translator')->trans($e->getMessage());
        } else {
            $translatedError = $this->get('translator')->trans($e);
        }
        $returnValue = array('status' => 'error', 'data' => $translatedError);
        return new Response(json_encode($returnValue), 500);
    }

}
