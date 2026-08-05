<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Api\Data;

/**
 * @api
 */
interface GaranImportResultInterface
{
    public const KEY_SUCCESS_COUNT = 'success_count';
    public const KEY_FAILED_COUNT = 'failed_count';
    public const KEY_ERRORS = 'errors';

    /**
     * @return int
     */
    public function getSuccessCount(): int;

    /**
     * @param int $count
     * @return $this
     */
    public function setSuccessCount(int $count): self;

    /**
     * @return int
     */
    public function getFailedCount(): int;

    /**
     * @param int $count
     * @return $this
     */
    public function setFailedCount(int $count): self;

    /**
     * @return \Solutioo\EuGuaranteeLabel\Api\Data\GaranImportErrorInterface[]
     */
    public function getErrors(): array;

    /**
     * @param \Solutioo\EuGuaranteeLabel\Api\Data\GaranImportErrorInterface[] $errors
     * @return $this
     */
    public function setErrors(array $errors): self;
}
