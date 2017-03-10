<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class GenerateFormsCommand extends Command
{
    const FORM_CLASS = <<<FORM
<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FormType{nr} extends AbstractType
{
    public function buildForm(FormBuilderInterface \$builder, array \$options)
    {
        \$builder->add('one');
        \$builder->add('two');
        \$builder->add('three');
        \$builder->add('four');
        \$builder->add('five');
    }
}
FORM;


    const FORM_TEST = <<<FORMTEST
<?php

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\FormType{nr};
use Symfony\Component\Form\Test\TypeTestCase;

class FormType{nr}Test extends TypeTestCase
{
    public function testSubmit()
    {
        \$formData = array(
            'one' => 'one',
            'two' => 'two',
            'three' => 'three',
            'four' => 'four',
            'five' => 'five',
        );

        \$form = \$this->factory->create(FormType{nr}::class);

        \$object = \$formData;

        \$form->submit(\$formData);

        \$this->assertTrue(\$form->isSynchronized());
        \$this->assertEquals(\$object, \$form->getData());

        \$view = \$form->createView();
        \$children = \$view->children;

        foreach (array_keys(\$formData) as \$key) {
            \$this->assertArrayHasKey(\$key, \$children);
        }
    }
}
FORMTEST;

    public function configure()
    {
        $this->setName('generate:forms')
            ->addOption('total', 't', InputOption::VALUE_REQUIRED, 'The number of forms to generate', 100);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $formDirectory = dirname(__DIR__).'/Form/Type';
        $testDirectory = dirname(__DIR__).'/../../tests/AppBundle/Form/Type';

        $fs = new Filesystem();

        foreach ([$formDirectory, $testDirectory] as $dir) {
            $fs->remove($dir);
            $fs->mkdir($dir);
        }

        $nr = (int) $input->getOption('total');

        $progress = new ProgressBar($output);

        $progress->start(($nr - 1) / 100);

        for ($i = 1; $i < $nr + 1; $i++) {

            if (0 === ($i % 100)) {
                $progress->advance(100);
            }

            $fs->dumpFile($formDirectory."/FormType{$i}.php", str_replace('{nr}', $i, self::FORM_CLASS));
            $fs->dumpFile($testDirectory."/FormType{$i}Test.php", str_replace('{nr}', $i, self::FORM_TEST));
        }

        $progress->finish();
    }
}