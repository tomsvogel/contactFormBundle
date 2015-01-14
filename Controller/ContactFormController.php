<?php

namespace Arkulpa\Bundle\ContactFormBundle\Controller;


use Arkulpa\Bundle\ContactFormBundle\Form\ContactFormType;
use Arkulpa\Bundle\ContactFormBundle\Form\ContactFormTypeWithPhone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactFormController extends Controller
{
    public function securityTokenAction($formId)
    {
        $formOptions = $this->getFormOptions($formId);
        $formType = $this->getFormType($formOptions['type']);
        $token = $this->get('form.csrf_provider')->generateCsrfToken($formType->getName());
        return $this->render('ArkulpaContactFormBundle:Form:token.html.twig', array('token' => $token));
    }

    public function sendAction(Request $request, $formId)
    {
        try {

            $formOptions = $this->getFormOptions($formId);
            $formType = $this->getFormType($formOptions['type']);
            $form = $this->get('form.factory')->create($formType, null, array('csrf_protection' => false));

            //map request data to names for the array
            $r = $request->request->all();
            foreach (array_keys($r) as $k) {
                $request->request->remove($k);
            }
            $request->request->set($formType->getName(), $r);
            $validationErrors = $this->get('arkulpa_contact_form_validator')->bindAndValidate($form, $request);
            if ($validationErrors !== null) {
                return $this->generateValidationErrorResponse($validationErrors);
            }

            $template = 'ArkulpaContactFormBundle:Email:default.html.twig';
            if (isset($formOptions['template'])) {
                $template = $formOptions['template'];
            }

            $body = $this->renderView($template, $r);


            $message = \Swift_Message::newInstance()
                ->setFrom(
                    array(
                        $r['email'] => $r['name']
                    )
                )
                ->setSubject($this->get('translator')->trans('contact-form-subject') . " - " . $r['subject'])
                ->setTo(explode(";", $formOptions['email']))
                ->setBody($body)->setContentType("text/html");

            if (!$this->get('mailer')->send($message)) {
                throw new \Exception('arkulpa-contact-form-send-mail-error');
            }

        } catch (\Exception $e) {
            return $this->generateLogicErrorResponse($e);
        }
        return $this->generateSuccesResponse();

    }


    protected function getFormOptions($formId)
    {
        $formOptions = $this->container->getParameter('arkulpa_contact_form_' . $formId);
        return $formOptions;
    }


    protected function getFormType($type)
    {
        $formType = null;
        switch ($type) {
            case "withPhone":
                $formType = new ContactFormTypeWithPhone();
                break;
            default:
                $formType = new ContactFormType();
                break;
        }

        return $formType;
    }

    protected function generateSuccesResponse($data = null)
    {
        $returnValue = array('status' => 'success', 'data' => $data);
        return new JsonResponse($returnValue);
    }

    protected function  generateValidationErrorResponse($validationErrors)
    {
        $returnValue = array('status' => 'fail', 'data' => array('errors' => $validationErrors));
        return new JsonResponse($returnValue, 400);
    }

    protected function generateLogicErrorResponse($e)
    {
        if ($e instanceof \Exception) {
            $translatedError = $this->get('translator.default')->trans($e->getMessage());
        } else {
            $translatedError = $this->get('translator.default')->trans($e);
        }
        $returnValue = array('status' => 'error', 'data' => $translatedError);
        return new JsonResponse($returnValue, 500);
    }

}
