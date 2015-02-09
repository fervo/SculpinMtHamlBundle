<?php

namespace Fervo\Sculpin\Bundle\MtHamlBundle;

use MtHaml\Environment;
use Sculpin\Core\Converter\ConverterContextInterface;
use Sculpin\Core\Converter\ConverterInterface;
use Sculpin\Core\Converter\ParserInterface;
use Sculpin\Core\Event\SourceSetEvent;
use Sculpin\Core\Sculpin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * MtHaml Converter.
 */
class MtHamlConverter implements ConverterInterface, EventSubscriberInterface
{
    /**
     * Haml environment
     *
     * @var Environment
     */
    protected $haml;

    /**
     * Extensions
     *
     * @var array
     */
    protected $extensions = array();

    /**
     * Constructor.
     *
     * @param Environment $haml
     * @param array       $extensions Extensions
     */
    public function __construct(Environment $haml, array $extensions = array())
    {
        $this->haml = $haml;
        $this->extensions = $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(ConverterContextInterface $converterContext)
    {
        $converterContext->setContent($this->render($converterContext));
    }

    private function render(ConverterContextInterface $converterContext)
    {
        $level = ob_get_level();
        ob_start();

        try {
            $this->display($converterContext);
        } catch (\Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }

        return ob_get_clean();
    }

    private function display(ConverterContextInterface $converterContext)
    {
        $hash = md5($converterContext->content());
        $funName = '__MtHamlTemplate_' . $hash;

        if (!function_exists($funName)) {
            $compiledCode = $this->haml->compileString($converterContext->content(), "source.haml");
            $compiledCode = $this->wrapCompiledCode($compiledCode, $funName);

            $content = eval($compiledCode);
        }

        $funName([]);
    }

    private function wrapCompiledCode($code, $funName)
    {
        // The code is wrapped in a function so that it can be parsed
        // once, and executed multiple times. This is faster than repeatedly
        // including the same PHP file.
        return <<<PHP
function $funName(\$__variables)
{
    extract(\$__variables);
?>$code<?php
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Sculpin::EVENT_BEFORE_RUN => 'beforeRun',
        );
    }

    /**
     * Before run
     *
     * @param SourceSetEvent $sourceSetEvent Source Set Event
     */
    public function beforeRun(SourceSetEvent $sourceSetEvent)
    {
        foreach ($sourceSetEvent->updatedSources() as $source) {
            foreach ($this->extensions as $extension) {
                if (fnmatch("*.{$extension}", $source->filename())) {
                    $source->data()->append('converters', SculpinMtHamlBundle::CONVERTER_NAME);
                    break;
                }
            }
        }
    }
}
