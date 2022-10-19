<?php

use PHPUnit\Framework\AssertionFailedError;
use Sinnbeck\DomAssertions\Asserts\ElementAssert;

it('can find the body', function () {
        $this->get('nesting')
            ->assertElement();
});

it('can find an element by selector', function () {
    $this->get('nesting')
        ->assertElement('#nav');
});

it('can fail finding anything', function () {
    $this->get('nesting')
        ->assertElement('div > nav');
})->throws(
    AssertionFailedError::class,
    'No element found with selector: div > nav'
);

it('can check the element has the correct type', function () {
    $this->get('nesting')
        ->assertElement('#nav', function (ElementAssert $element) {
            $element->is('nav');
        });
});

it('can fail matching element type', function () {
    $this->get('nesting')
        ->assertElement('#nav', function (ElementAssert $element) {
            $element->is('div');
        });
})->throws(
    AssertionFailedError::class,
    'Element is not of type "div"'
);


it('can find a nested element', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->containsDiv();
        }, 'div');
});

it('can find a nested element with content', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->containsDiv([
                'class' => 'foobar'
            ]);
        }, 'div');
});

it('can find a nested element with content functional', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->containsDiv(function (ElementAssert $element) {
                $element->is('div');
            });
        }, 'div');
});

it('can find a nested element multiple levels', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->containsDiv(function (ElementAssert $element) {
                $element->is('div');
                $element->contains('div', function (ElementAssert $element) {
                    $element->is('div');
                    $element->containsDiv(function (ElementAssert $element) {
                        $element->is('div');
                    });
                });
            });
        }, 'div');
});

it('can find a nested element multiple levels by query', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->containsDiv(function (ElementAssert $element) {
                $element->is('div');
                $element->contains('.deep', function (ElementAssert $element) {
                    $element->is('div');
                    $element->containsSpan(function (ElementAssert $element) {
                        $element->is('span');
                    });
                });
            });
        }, 'div');
});

it('can find a nested element multiple levels by query and attributes', function () {
    $this->get('nesting')
        ->assertElement(function (ElementAssert $element) {
            $element->containsDiv(function (ElementAssert $element) {
                $element->is('div');
                $element->contains('.deep', [
                    'class' => 'deep'
                ]);
            });
        }, 'div');
});