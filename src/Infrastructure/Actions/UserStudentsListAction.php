<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romanpravda\KcTestTask\Domains\Student\Student;
use Romanpravda\KcTestTask\Domains\Student\StudentRepositoryInterface;
use Romanpravda\KcTestTask\Exceptions\Domains\Token\TokenNotFound;
use Romanpravda\KcTestTask\Exceptions\Repositories\PDOException;
use Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface;
use Romanpravda\KcTestTask\Infrastructure\Http\Response;
use Romanpravda\KcTestTask\Support\Criteria\Criteria;
use Romanpravda\KcTestTask\Support\Criteria\Filters\EqualsFilter;
use Romanpravda\KcTestTask\Support\Criteria\Order;

final class UserStudentsListAction
{
    /**
     * Student's repository.
     *
     * @var \Romanpravda\KcTestTask\Domains\Student\StudentRepositoryInterface
     */
    private StudentRepositoryInterface $repository;

    /**
     * Response's factory.
     *
     * @var \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;

    /**
     * UserStudentsListAction constructor.
     *
     * @param \Romanpravda\KcTestTask\Domains\Student\StudentRepositoryInterface $repository
     * @param \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface $responseFactory
     */
    public function __construct(StudentRepositoryInterface $repository, ResponseFactoryInterface $responseFactory)
    {
        $this->repository = $repository;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Students list for user.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $perPage = $request->getQueryParams()['per-page'] ?? 25;
            $page = $request->getQueryParams()['page'] ?? 1;

            /** @var \Romanpravda\KcTestTask\Domains\User\User|null $user */
            $user = $request->getAttribute('user');
            if (is_null($user)) {
                throw TokenNotFound::create();
            }

            $userId = $user->getId();
            $filter = new EqualsFilter('user_id', $userId);

            $criteria = new Criteria($filter, Order::asc('created_at'), $perPage, ($page - 1) * $perPage);
            $students = $this->repository->findByCriteria($criteria);

            return $this->responseFactory->create(new Response([
                'data' => array_map(static fn (Student $student) => [
                    'id' => $student->getId(),
                    'fullName' => $student->getFullName(),
                    'group' => $student->getGroup(),
                ], $students),
            ]));
        } catch (TokenNotFound $exception) {
            return $this->responseFactory->create((new Response([
                'error' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]))->setHttpCode(401));
        } catch (PDOException $exception) {
            return $this->responseFactory->create((new Response([
                'error' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]))->setHttpCode(500));
        }
    }
}