<?php

namespace App\Controller;

use App\Form\ArrayAnalyzerFormType;
use App\Service\ArrayAnalyzerService;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArrayAnalyzerController extends AbstractController
{
    public function __construct(private readonly ArrayAnalyzerService $analyzer)
    {
    }

    /**
     * @throws RandomException
     */
    #[Route('/', name: 'app_array_analyzer', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ArrayAnalyzerFormType::class);
        $form->handleRequest($request);

        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string,mixed> $data */
            $data = (array) $form->getData();

            $numbers = $this->analyzer->prepareArray($data);
            $strategyKey = isset($data['strategy']) && is_string($data['strategy']) ? $data['strategy'] : 'uniqueness';
            $result = $this->analyzer->analyze($numbers, $strategyKey);
        }

        return $this->render('array_analyzer/index.html.twig', [
            'form' => $form->createView(),
            'result' => $result,
        ]);
    }
}
