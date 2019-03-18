<?php

namespace App\Command;

use App\Entity\RecurringPayment;
use App\Entity\Request;
use App\Event\RequestSuccessEvent;
use App\Service\UnitellerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RecurringRequestCommand extends Command
{
    protected static $defaultName = 'app:recurring-requests';

    /**
     * @var UnitellerService
     */
    private $unitellerService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * RecurringRequestCommand constructor.
     *
     * @param UnitellerService         $unitellerService
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $dispatcher
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(
        UnitellerService $unitellerService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $dispatcher
    ) {
        $this->unitellerService = $unitellerService;
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Checks needed and send recurring payment request');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var RecurringPayment[] $rp */
        $rp = $this->entityManager->getRepository(RecurringPayment::class)->findNeedRequest();
        /** @var Request[] $requests */
        $requests = [];

        foreach ($rp as $v) {
            $request = $v->getRequest();
            $new_request = (new Request())
                ->setSum($request->getSum())
                ->setUser($v->getUser())
                ->setChild($request->getChild())
                ->setRecurent(true);
            $this->entityManager->persist($new_request);
            $requests[] = $new_request;
        }

        $this->entityManager->flush();

        $opts = [
            'https' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'protocol_version' => 1.1,
                'timeout' => 1,
                'ignore_errors' => true
            ]
        ];

        foreach ($requests as $k => $v) {
            $opts['https']['content'] = http_build_query(
                $this->unitellerService->getRecurringForm($v, $rp[$k]->getRequest())
            );
            $opts['https']['header']['Content-Length'] = strlen($opts['https']['content']);
            $response = explode("\r\n", file_get_contents(
                $this->unitellerService::RECURRING_URL,
                false,
                stream_context_create($opts)
            ));

            foreach ($response as $key => $str) {
                $response[$key] = str_getcsv($str, ';');
            }

            if ('ErrorCode' == $response[0][0]) {
                $io->warning($response[1][1]);

                continue;
            }

            $io->text($response[1]);
            $this->entityManager->persist($v->setStatus(2));
            $this->entityManager->persist($rp[$k]->setWithdrawalAt(new \DateTime()));
            $this->dispatcher->dispatch(RequestSuccessEvent::RECURRING_NAME, new RequestSuccessEvent($v));
        }

        $this->entityManager->flush();
        $io->success('Success');
    }
}
