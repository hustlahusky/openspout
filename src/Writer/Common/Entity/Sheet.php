<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Entity;

use OpenSpout\Writer\Common\Manager\SheetManager;
use OpenSpout\Writer\XLSX\Entity\SheetView;

/**
 * External representation of a worksheet.
 */
final class Sheet
{
    public const DEFAULT_SHEET_NAME_PREFIX = 'Sheet';

    /** @var 0|positive-int Index of the sheet, based on order in the workbook (zero-based) */
    private int $index;

    /** @var string ID of the sheet's associated workbook. Used to restrict sheet name uniqueness enforcement to a single workbook */
    private string $associatedWorkbookId;

    /** @var string Name of the sheet */
    private string $name;

    /** @var bool Visibility of the sheet */
    private bool $isVisible;

    /** @var SheetManager Sheet manager */
    private SheetManager $sheetManager;

    private ?SheetView $sheetView = null;

    /** @var 0|positive-int */
    private int $writtenRowCount = 0;

    /**
     * @param 0|positive-int $sheetIndex           Index of the sheet, based on order in the workbook (zero-based)
     * @param string         $associatedWorkbookId ID of the sheet's associated workbook
     * @param SheetManager   $sheetManager         To manage sheets
     */
    public function __construct(int $sheetIndex, string $associatedWorkbookId, SheetManager $sheetManager)
    {
        $this->index = $sheetIndex;
        $this->associatedWorkbookId = $associatedWorkbookId;

        $this->sheetManager = $sheetManager;
        $this->sheetManager->markWorkbookIdAsUsed($associatedWorkbookId);

        $this->setName(self::DEFAULT_SHEET_NAME_PREFIX.($sheetIndex + 1));
        $this->setIsVisible(true);
    }

    /**
     * @return 0|positive-int Index of the sheet, based on order in the workbook (zero-based)
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    public function getAssociatedWorkbookId(): string
    {
        return $this->associatedWorkbookId;
    }

    /**
     * @return string Name of the sheet
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of the sheet. Note that Excel has some restrictions on the name:
     *  - it should not be blank
     *  - it should not exceed 31 characters
     *  - it should not contain these characters: \ / ? * : [ or ]
     *  - it should be unique.
     *
     * @param string $name Name of the sheet
     *
     * @throws \OpenSpout\Writer\Exception\InvalidSheetNameException if the sheet's name is invalid
     */
    public function setName(string $name): self
    {
        $this->sheetManager->throwIfNameIsInvalid($name, $this);

        $this->name = $name;

        $this->sheetManager->markSheetNameAsUsed($this);

        return $this;
    }

    /**
     * @return bool isVisible Visibility of the sheet
     */
    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    /**
     * @param bool $isVisible Visibility of the sheet
     */
    public function setIsVisible(bool $isVisible): self
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    /**
     * @return $this
     */
    public function setSheetView(SheetView $sheetView): self
    {
        $this->sheetView = $sheetView;

        return $this;
    }

    public function getSheetView(): ?SheetView
    {
        return $this->sheetView;
    }

    /**
     * @internal
     */
    public function incrementWrittenRowCount(): void
    {
        ++$this->writtenRowCount;
    }

    /**
     * @return 0|positive-int
     */
    public function getWrittenRowCount(): int
    {
        return $this->writtenRowCount;
    }
}
