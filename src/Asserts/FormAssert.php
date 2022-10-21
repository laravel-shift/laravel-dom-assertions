<?php

namespace Sinnbeck\DomAssertions\Asserts;

use Illuminate\Support\Str;
use Illuminate\Testing\Assert as PHPUnit;
use PHPUnit\Framework\Assert;

class FormAssert extends BaseAssert
{
    public function hasAction(string $action): self
    {
        PHPUnit::assertEquals(
            Str::of($this->getAttributeFromForm('action'))->lower()->finish('/')->start('/'),
            Str::of($action)->lower()->finish('/')->start('/'),
            sprintf('Could not find an action on the form with the value %s', $action)
        );

        return $this;
    }

    public function hasMethod(string $method): self
    {
        if (! in_array(strtolower($method), [
            'get',
            'post',
        ])) {
            return $this->hasSpoofMethod($method);
        }

        PHPUnit::assertEquals(
            Str::of($this->getAttributeFromForm('method'))->lower(),
            Str::of($method)->lower(),
            sprintf('Could not find a method on the form with the value %s', $method)
        );

        return $this;
    }

    public function hasSpoofMethod(string $type): self
    {
        $element = $this->parser->query('input[type="hidden"][name="_method"]');
        Assert::assertNotNull(
            $element,
            sprintf('No spoof methods was found in form!')
        );

        Assert::assertEquals(
            Str::lower($type),
            Str::lower($this->getAttributeFor($element, 'value')),
            sprintf('No spoof method for %s was found in form!', $type)
        );

        return $this;
    }

    public function hasCSRF(): self
    {
        Assert::assertNotNull(
            $this->parser->query('input[type="hidden"][name="_token"]'),
            'No CSRF was found in form!');

        return $this;
    }

    protected function getAttributeFromForm(string $attribute)
    {
        return $this->parser->getAttributeForRoot($attribute);
    }

    public function findSelect($selector = 'select', $callback = null): static
    {
        if (is_callable($selector)) {
            $callback = $selector;
            $selector = 'select';
        }

        if (! $select = $this->getParser()->query($selector)) {
            Assert::fail(sprintf('No select found for selector: %s', $selector));
        }

        $callback(new SelectAssert($this->getContent(), $select));

        return $this;
    }
}
