<?php

namespace App\Controller;

use App\Entity\Admins;
use App\Repository\AdminsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Scores;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ScoresController extends AbstractController
{
    /**
     * @Route("/scores", name="scores")
     */
    public function index(Request $request): Response
    {
        $params = array_merge([
            'order' => 'score',
            'orderBy' => 'DESC',
            'filter' => ''
        ], $request->query->all());

        $scoresRepository = $this->getDoctrine()->getRepository(Scores::class);

        if ($request->getSession()->get('logged_in'))
            if (isset($params['verify']) || isset($params['unverify']))
                $scoresRepository->verifyScore(
                    isset($params['verify']) ? $params['verify'] : $params['unverify'],
                    isset($params['verify']) ? 1 : 0
                );

		return $this->render('scores.html.twig', [
            'scores' => 'ScoresController',
            'uri' => $request->getPathInfo(),
            'data' => $scoresRepository->show(0, $params['order'], $params['orderBy'], $params['filter'], $request),
            'params' => $params,
            'logged_in' => $request->getSession()->get('logged_in')
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $params = array_merge([
            'inputUname' => isset($_POST['inputUname']) ? $_POST['inputUname'] : null,
            'inputPassword' => isset($_POST['inputPassword']) ? $_POST['inputPassword'] : null
        ], $request->query->all());

        $adminsRepository = $this->getDoctrine()->getRepository(Admins::class);

        if ($params['inputUname'] && $params['inputPassword'])
            if ($adminsRepository->authenticate($request, $params['inputUname'], $params['inputPassword']))
                return $this->redirect('/scores');

        return $this->render('login.html.twig', [
            'scores' => 'ScoresController',
            'params' => $params
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $request->getSession()->get('logged_in', false);
        $request->getSession()->invalidate();
        return $this->redirect('/scores');
    }

    /**
     * @Route("/api/getScores", name="getScores")
     */
    public function getScores(Request $request)
    {
        $params = array_merge([
            'order' => 'score',
            'orderBy' => 'DESC',
            'filter' => ''
        ], $request->query->all());

        $scoresRepository = $this->getDoctrine()->getRepository(Scores::class);

        return new JsonResponse($scoresRepository->show(0, $params['order'], $params['orderBy'], $params['filter'], $request));
    }

    /**
     * @Route("/api/submitScore/{name}/{difficulty}/{score}", name="submitScore")
     */
    public function submitScore($name, $difficulty, $score)
    {
        $scoresRepository = $this->getDoctrine()->getRepository(Scores::class);

        return new JsonResponse([$scoresRepository->submitScore($name, $difficulty, $score)]);
    }
}
