<?php
declare(strict_types = 1);

namespace Mike\Utils\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Product extends Command
{
    use \Mike\Utils\Helper\ObjectManagerTrait;

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "type";

    public function init()
    {
        $this->state = $this->getOm()->get(\Magento\Framework\App\State::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface  $input,
        OutputInterface $output
    )
    {

        $name = $input->getArgument(self::NAME_ARGUMENT);
        $optionType = $input->getOption('type');

        $this->init();
        try {
            switch ($name) {
                case 'delete':
                    /** @var \Mike\Utils\Helper\Product $productHelper */
                    $productHelper = $this->getOm()->get(\Mike\Utils\Helper\Product::class);

                    # php bin/magento utils:product delete -t sku
                    if ($optionType == 'sku') {
                        $skus = ['112540', '203000', '251013', '284050', '299050', '352828', '354028', '357211', '357311', '357426', '357526', '388050', '388150', '389050', '389950', '402045', '403248', '409045', '409945', '441944', '448244', '448544', '483044', '483544', '483944', '487011', '487311', '489044', '489944', '500288', '500993', '501288', '501988', '502588', '505288', '505588', '506288', '506993', '508088', '512093', '521284', '530084', '536084', '540288', '541988', '542188', '543088', '545188', '546188', '563687', '570088', '570688', '575188', '576088', '581981', '582581', '585581', '590989', '595089', '612030', '640230', '641246', '660600', '662783', '662883', '662983', '664147', '664648', '682042', '687142', '687242', '696151', '752116', '752516', '773015', '773915', '774015', '775015', '778915', '779015', '779515', '801211', '831111', '901552', '902426', '904200', '904400', '904500', '906284', '906381', '906581', '906800', '907400', '907500', '907900', '909300', '909480', '910581', '910584', '911981', '912000', '912400', '912806', '912906', '914288', '914400', '914500', '915182', '915193', '916081', '916800', '917600', '917995', '919000', '919589'];
                        $output->writeln("Delete products by skus:");

                        $productHelper->deleteProductBySkus($skus, true);

                        $output->writeln("Done.");
                    } else {
                        # php bin/magento utils:product delete
                        $ids = [];
                        $output->writeln("Delete products by ids:");

                        $productHelper->deleteProductByIds($ids, true);

                        $output->writeln("Done.");
                    }
                    break;
            }
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
//            $this->logger->error($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("utils:product");
        $this->setDescription("Product utilities command");
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name"),
            new InputOption(self::NAME_OPTION, "-t", InputOption::VALUE_OPTIONAL, "Sku or id type")
        ]);
        parent::configure();
    }
}

