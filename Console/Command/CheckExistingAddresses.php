<?php

namespace Hryvinskyi\QuoteAddressValidator\Console\Command;

use Hryvinskyi\QuoteAddressValidator\Model\AddressValidationInterface;
use Magento\Quote\Model\ResourceModel\Quote\Address\CollectionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckExistingAddresses extends Command
{
    /**
     * @var CollectionFactory 
     */
    private $quoteAddressCollectionFactory;

    /**
     * @var AddressValidationInterface 
     */
    private $addressValidation;
    
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');
        $infoStyle = new OutputFormatterStyle('white', 'blue');
        $output->getFormatter()->setStyle('info', $infoStyle);
        $formattedInfoBlock = $formatter->formatBlock(['INFO:', 'Check existing addresses in the database'], 'info', TRUE);

        $output->writeln('');
        $output->writeln($formattedInfoBlock);
        $output->writeln('');


        $successStyle = new OutputFormatterStyle('white', 'green');
        $output->getFormatter()->setStyle('success', $successStyle);

        // Get all addresses
        $collection = $this->quoteAddressCollectionFactory->create();
        $collection->addFieldToSelect('*');
        $collection->setPageSize(50);
        $pages = $collection->getLastPageNumber();
        $foundAddresses = [];
        $found = 0;

        for ($pageNum = 1; $pageNum <= $pages; $pageNum++) {
            $collection->setCurPage($pageNum);
            foreach ($collection as $item) {
                foreach ($this->addressValidation->getValidations() as $validation) {
                    try {
                        $validation->execute($item);
                    } catch (\Exception $e) {
                        $foundAddresses[$item->getId()] = 1;
                        $found++;
                        $addressType = $item->getAddressType() ? '<success>' . $item->getAddressType() . '</success>' : '<error>EMPTY</error>';
                        $output->writeln('Address type: ' . $addressType . '</info>; ID: <success>' . $item->getId() . '</success>; Message: <fg=#c0392b;bg=black>' . $e->getMessage() . '</>');
                    }
                }
            }

            $collection->clear();
        }

        $output->writeln('');

        if ($found === 0) {
            $infoStyle = new OutputFormatterStyle('white', 'green');
            $output->getFormatter()->setStyle('info', $infoStyle);
            $formattedInfoBlock = $formatter->formatBlock(['RESULT:', 'All addresses are valid'], 'info', TRUE);
            $output->writeln($formattedInfoBlock);
        } else {
            $infoStyle = new OutputFormatterStyle('white', 'red');
            $output->getFormatter()->setStyle('info', $infoStyle);
            $formattedInfoBlock = $formatter->formatBlock(['RESULT:', 'We found ' . array_sum($foundAddresses) . ' addresses with issues and ' . $found . ' fields which do not pass validation. Please update validation regex.'], 'info', TRUE);
            $output->writeln($formattedInfoBlock);
        }

        return 0;
    }
}
