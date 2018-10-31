<?php declare(strict_types=1);

/**
 * This file is part of the "NFQ Bundles" package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nfq\AdminBundle\Service\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Nfq\AdminBundle\Event\GenericEvent;
use Nfq\AdminBundle\Service\Generic\Actions\GenericActionsInterface;
use Nfq\AdminBundle\Service\Generic\Search\GenericSearchInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractAdminManager
 * @package Nfq\AdminBundle\Service\Admin
 */
abstract class AbstractAdminManager implements AdminManagerInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var GenericSearchInterface */
    protected $search;

    /** @var GenericActionsInterface */
    protected $actions;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setActions(GenericActionsInterface $actions): void
    {
        $this->actions = $actions;
    }

    public function setSearch(GenericSearchInterface $search): void
    {
        $this->search = $search;
    }

    public function delete(
        object $entity,
        string $beforeEventName = 'generic.before_delete',
        string $afterEventName = 'generic.after_delete'
    ): object {
        $beforeEvent = new GenericEvent($entity, $beforeEventName);
        $afterEvent = new GenericEvent($entity, $afterEventName, 'admin.generic.message.deleted_successfully');

        $this->actions->delete($beforeEvent, $entity, $afterEvent);

        return $entity;
    }

    public function insert(
        object $entity,
        string $beforeEventName = 'generic.before_insert',
        string $afterEventName = 'generic.after_insert'
    ): object {
        $beforeEvent = new GenericEvent($entity, $beforeEventName);
        $afterEvent = new GenericEvent($entity, $afterEventName, 'admin.generic.message.saved_successfully');

        $this->actions->save($beforeEvent, $entity, $afterEvent);

        return $entity;
    }

    public function save(
        object $entity,
        string $beforeEventName = 'generic.before_save',
        string $afterEventName = 'generic.after_save'
    ): object {
        $beforeEvent = new GenericEvent($entity, $beforeEventName);
        $afterEvent = new GenericEvent($entity, $afterEventName, 'admin.generic.message.saved_successfully');

        $this->actions->save($beforeEvent, $entity, $afterEvent);

        return $entity;
    }

    public function getEntities(Request $request): Query
    {
        return $this->search->getResults($request);
    }
}
