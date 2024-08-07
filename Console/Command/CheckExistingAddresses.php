<?php

namespace Hryvinskyi\QuoteAddressValidator\Console\Command;

use Hryvinskyi\QuoteAddressValidator\Model\AddressValidationInterface;
use Magento\Quote\Model\ResourceModel\Quote\Address\CollectionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckExistingAddresses extends Command
{
    private CollectionFactory $quoteAddressCollectionFactory;
    private AddressValidationInterface $addressValidation;

    public function __construct(
        CollectionFactory $quoteAddressCollectionFactory,
        AddressValidationInterface $addressValidation
    ) {
        $this->quoteAddressCollectionFactory = $quoteAddressCollectionFactory;
        $this->addressValidation = $addressValidation;
        parent::__construct();
    }

    /**
     * Initialization of the command.
     */
    protected function configure()
    {
        $this->setName('hryvinskyi:quote-address-validator:check-existing-addresses');
        $this->setDescription('Check existing addresses in the database');
        parent::configure();
    }

    /**
     * CLI command description.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Check existing addresses in the database');

        // Get all addresses
        $collection = $this->quoteAddressCollectionFactory->create();
        $collection->addFieldToSelect('*');
        $collection->setPageSize(50);
        $pages = $collection->getLastPageNumber();
        for ($pageNum = 1; $pageNum <= $pages; $pageNum++) {
            $collection->setCurPage($pageNum);
            foreach ($collection as $item) {
                try {
                    $this->addressValidation->validate($item);
                } catch (\Exception $e) {
                    $output->writeln('Address type: ' . $item->getAddressType() . ' ID: ' . $item->getId() . ' - ' . $e->getMessage());
                }
            }
            $collection->clear();
        }
    }

}
