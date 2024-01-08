<?php

namespace Morpheus\Shared\Traits;

trait HasBlocks
{
    use UseACF;
    public array $blocks = [];

    public string $blocksPath = MORPHEUS_CORE_PATH . "src/blocks";
    public string $blocksNamespace = 'Morpheus\\blocks';

    public function registerBlocks(): void
    {
        $blocks = include $this->blocksPath . "/register.php";
        foreach ($blocks as $block) {
            $this->registerBlock($block);
        }
        $this->registerAcf();
    }

    public function registerBlock(string $blockName, array $options = []): void
    {
        $blockPath = "{$this->blocksPath}/{$blockName}";
        $blockJsonPath = (file_exists("{$blockPath}/build/block.json"))
            ? "{$blockPath}/build/block.json"
            : "{$blockPath}/block.json";

        if (!file_exists($blockJsonPath)) return;
        $metadata = register_block_type($blockJsonPath, $options);
        // debug($metadata);

        if ($metadata?->morpheus?->init && file_exists($blockPath . "/Block.php")) {
            $blockClass = "{$this->blocksNamespace}\\{$this->getBlockFilteredName($blockName)}\\Block";
            $this->blocks[$blockName] = new $blockClass;
        }

        if (!file_exists($blockPath . "/acf.json")) return;
        $this->addAcfItem($blockPath . "/acf.json");
    }

    public function getBlockFilteredName(string $blockName): string
    {
        return preg_replace('/^[^\/]\/(.+)$/', '$1', $blockName);
    }
}
