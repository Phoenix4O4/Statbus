<?php

namespace App\Controller\TGDB\Tickets;

use App\Controller\Controller;
use App\Domain\Ticket\Repository\TicketRepository;
use App\Enum\TicketActions;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBTicketViewerController extends Controller
{
    #[Inject]
    private TicketRepository $ticketRepository;

    public function action(): ResponseInterface
    {
        $ticket = $this->ticketRepository->getSingleTicket($this->getArg('round'), $this->getArg('ticket'))->getResults();
        $badges = [];
        $triggeredBan = false;
        foreach($ticket as $t) {
            $badges[] = $t->getSenderBadge();
            $badges[] = $t->getRecipientBadge();
            if(TicketActions::INTERACTION === $t->getAction()) {
                if(str_contains($t->getMessage(), 'server ban') || str_contains($t->getMessage(), 'role ban')) {
                    $triggeredBan = true;
                }
            }
        }

        return $this->render('tgdb/tickets/single.html.twig', [
            'ticket' => $ticket,
            'badges' => array_filter(array_unique($badges)),
            'triggeredBan' => $triggeredBan
        ]);
    }
}
