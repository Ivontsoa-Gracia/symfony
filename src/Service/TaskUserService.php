<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TaskUserService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Ajouter un utilisateur à une tâche
    public function assignUserToTask(Task $task, User $user): void
    {
        $task->addAssignee($user);

        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }
}
?>