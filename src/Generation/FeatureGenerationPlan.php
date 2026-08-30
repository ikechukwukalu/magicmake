<?php

namespace Ikechukwukalu\Magicmake\Generation;

class FeatureGenerationPlan extends GenerationPlan
{
    /** @var string */
    private $profile;

    /** @var GenerationTarget */
    private $target;

    /** @var array<int, string> */
    private $artifacts;

    public function __construct($basePath, $profile, GenerationTarget $target, array $artifacts)
    {
        parent::__construct($basePath);
        $this->profile = $profile;
        $this->target = $target;
        $this->artifacts = $artifacts;
    }

    public function profile()
    {
        return $this->profile;
    }

    public function target()
    {
        return $this->target;
    }

    public function artifacts()
    {
        return $this->artifacts;
    }
}
