<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class SecurityController extends AbstractController
{
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    public function redirectToLogin(): RedirectResponse
    {
        return new RedirectResponse('/login');
    }

    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    public function forgotPasswordRequest(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userForm = $form->get('usuario')->getData();

            $userValidate = $this->getDoctrine()->getRepository(Usuario::class)->findOneBy(['username' => $userForm]);

            if ($userValidate) {

                // primero buscar email de contactos
                $contactos = $userValidate->getContacto();
                foreach ($contactos as $contacto) {
                    $email = $contacto->getEmail(); //me quedo con el último mail
                }

                // si no hay elige el de la empresa
                if (!$email) {
                    $email = $userValidate->getEmpresa()->getEmail();
                }

                if ($email) {
                    // Generar y guardar token de restablecimiento de contraseña
                    $token = uniqid();
                    $userValidate->setResetToken($token);
                    $userValidate->setTokenExpiry(new \DateTime('+1 hour'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($userValidate);
                    $entityManager->flush();

                    // Enviar correo electrónico de restablecimiento de contraseña
                    $this->sendResetPasswordEmail($userValidate, $mailer, $email);
                }
            } else {
                $this->addFlash('error', 'El usuario ' . $userForm . ' NO existe :(');
            }


            return $this->redirectToRoute('forgot_password_request');
        }

        return $this->render('auth/contrasenya_olvidada.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function resetPassword(Request $request, $token, UserPasswordHasherInterface $passwordHasher)
    {
        $user = $this->getDoctrine()->getRepository(Usuario::class)->findOneBy(['resetToken' => $token]);

        if (!$user || $user->getTokenExpiry() < new \DateTime()) {
            $this->addFlash('error', 'El enlace de restablecimiento de contraseña no es válido o ha caducado.');
            return $this->redirectToRoute('forgot_password_request');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $textoplanoPassword = $form->get('password')->getData();

            $confirmPassword = $form->get('confirm_password')->getData();

            // Validar las contraseñas
            $error = $this->validarPassword($textoplanoPassword, $confirmPassword);
            if ($error !== null) {
                $this->addFlash('error', $error);
                return $this->redirectToRoute('reset_password', ['token' => $token]);
            }

            // encriptamos la pass
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $textoplanoPassword
            );

            $user->setPassword($hashedPassword);
            $user->setResetToken(null);
            $user->setTokenExpiry(null);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Tu contraseña ha sido restablecida correctamente.');
            return $this->redirectToRoute('login');
        }

        return $this->render('auth/reset_contrasenya.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Envía un correo electrónico para solicitar el restablecimiento de contraseña a un usuario.
     *
     * @param Usuario          $user    El usuario al que se enviará el correo electrónico.
     * @param MailerInterface  $mailer  El servicio de mailer para enviar el correo electrónico.
     * @param string           $destino El destino del correo electrónico (la dirección de correo electrónico del usuario).
     *
     * @return void
     */
    private function sendResetPasswordEmail(Usuario $user, MailerInterface $mailer, string $destino)
    {

        $email = (new TemplatedEmail())
            ->from(new Address('myproject@granota.net', 'Granota'))
            ->to(new Address($destino))
            ->subject('Solicitud de restablecimiento de contraseña')
            ->htmlTemplate('plantillas/email-reset_password.html.twig')
            ->context([
                'user' => $user,
            ]);

        try {
            $mailer->send($email);
            $this->addFlash('success', 'Se ha enviado un correo electrónico a ' . $destino . ' tienes 1 hora para restablecer tu contraseña ');
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', $e->getMessage());
        }
    }

    public function validarPassword(string $password, string $confirmPassword): ?string
    {
        // Validar que la contraseña y la confirmación de la contraseña no estén vacías
        if (empty($password) || empty($confirmPassword)) {
            return 'La contraseña y la confirmación de la contraseña son obligatorias.';
        }

        // Validar que la contraseña y la confirmación de la contraseña coincidan
        if ($password !== $confirmPassword) {
            return 'Las contraseñas no coinciden.';
        }

        // Validar la longitud mínima de la contraseña
        if (strlen($password) < 6) {
            return 'La contraseña debe tener al menos 6 caracteres.';
        }

        // Validar que la contraseña no contenga caracteres especiales que puedan causar inyección SQL
        if (!preg_match('/^[a-zA-Z0-9]+$/', $password)) {
            return 'La contraseña no puede contener caracteres especiales.';
        }

        // Si todas las validaciones son exitosas, retorna null
        return null;
    }
}
