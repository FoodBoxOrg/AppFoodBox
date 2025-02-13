<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    #[Route('/change-locale/{locale}', name: 'change_locale')]
    public function changeLocale(string $locale, Request $request, SessionInterface $session): RedirectResponse
    {
        // Stocker la nouvelle langue dans la session
        $session->set('_locale', $locale);

        // Rediriger vers la page précédente ou la page d'accueil si non disponible
        return $this->redirect($request->headers->get('referer') ?: $this->generateUrl('app_homepage'));
    }
}
