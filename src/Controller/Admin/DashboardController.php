<?php

namespace App\Controller\Admin;

use App\Repository\ParsedNewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private ParameterBagInterface $params;
    private ParsedNewsRepository $parsedNewsRepository;
    private EntityManagerInterface $em;

    public function __construct(
        ParameterBagInterface $params,
        ParsedNewsRepository $parsedNewsRepository,
        EntityManagerInterface $em
    )
    {
        $this->params = $params;
        $this->parsedNewsRepository = $parsedNewsRepository;
        $this->em = $em;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $sites = $this->params->get('sites');

        $news = $this->parsedNewsRepository->findAll();

        return $this->render(
            "admin/dashboard.html.twig",
            [
                'sites' => $sites,
                'news'  => $news
            ]
        );
    }

    #[Route('/admin/run_parser', name: 'run_parser')]
    public function runParser():Response
    {
        $news = $this->parsedNewsRepository->findAll();

        foreach ($news as $one_news) {
            $this->em->remove($one_news);
        }

        $this->em->flush();

        $source = $_GET['source'];
        $news_count = $_GET['count'];
        $sites = $this->params->get('sites');

        $pathToBinConsole = __DIR__ . '/../../../bin/console';

        $command = "$pathToBinConsole parser:start $source $news_count";

        exec($command, $output, $returnCode);

        $news = $this->parsedNewsRepository->findAll();

        return $this->render(
            "admin/dashboard.html.twig",
            [
                'sites' => $sites,
                'news'  => $news
            ]
        );
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symfony App');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Run Parse News Command', 'fas fa-newspaper', 'run_parse_news_command');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}