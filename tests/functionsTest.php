<?php

declare(strict_types=1);

namespace Zlikavac32\Enum\Tests;

use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;
use function Zlikavac32\Enum\assertFqnIsEnumClass;
use function Zlikavac32\Enum\assertNoParentHasEnumerateMethodForClass;
use Zlikavac32\Enum\Tests\Fixtures\EnumThatExtendsNonAbstractEnumWithoutEnumerate;
use Zlikavac32\Enum\Tests\Fixtures\EnumThatExtendsValidEnum;
use Zlikavac32\Enum\Tests\Fixtures\NonAbstractEnum;
use Zlikavac32\Enum\Tests\Fixtures\ValidEnumWithOneParent;
use Zlikavac32\Enum\Tests\Fixtures\ValidStringEnum;
use function sprintf;
use function Zlikavac32\Enum\assertElementNameIsString;
use function Zlikavac32\Enum\assertEnumClassIsAbstract;
use function Zlikavac32\Enum\assertValidEnumCollection;
use function Zlikavac32\Enum\assertValidEnumElementObjectType;
use function Zlikavac32\Enum\assertValidNamePattern;

class functionsTest extends TestCase
{
    public function testThatFqnRepresentingValidEnumClassPassesAssert(): void
    {
        try {
            assertFqnIsEnumClass(ValidStringEnum::class);

            $this->assertTrue(true);
        } catch (Throwable $e) {
            $this->failWithThrowable($e);
        }
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage stdClass does not have Zlikavac32\Enum\Enum as it's parent
     */
    public function testThatFqnNotRepresentingValidEnumClassDoesNotPassAssert(): void
    {
        assertFqnIsEnumClass(stdClass::class);
    }

    public function testThatNoExceptionIsThrownOnValidName(): void
    {
        try {
            assertValidNamePattern('ABC');

            $this->assertTrue(true);
        } catch (Throwable $e) {
            $this->failWithThrowable($e);
        }
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Element name "FO*BAR" does not match pattern /^[a-zA-Z_][a-zA-Z_0-9]*$/
     */
    public function testThatExceptionIsThrownOnInvalidName(): void
    {
        assertValidNamePattern('FO*BAR');
    }

    public function testThatNoExceptionIsThrownOnAbstractClass(): void
    {
        try {
            assertEnumClassIsAbstract(ValidStringEnum::class);

            $this->assertTrue(true);
        } catch (Throwable $e) {
            $this->failWithThrowable($e);
        }
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Enum Zlikavac32\Enum\Tests\Fixtures\NonAbstractEnum must be declared as abstract
     */
    public function testThatExceptionIsThrownOnNonAbstractClass(): void
    {
        assertEnumClassIsAbstract(NonAbstractEnum::class);
    }

    public function testThatNoExceptionIsThrownWhenElementNameIsString(): void
    {
        try {
            assertElementNameIsString(ValidStringEnum::class, 'abcd');

            $this->assertTrue(true);
        } catch (Throwable $e) {
            $this->failWithThrowable($e);
        }
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Element name (object instance of stdClass) in enum Zlikavac32\Enum\Tests\Fixtures\ValidStringEnum is not valid
     */
    public function testThatExceptionIsThrownWhenElementNameIsObject(): void
    {
        assertElementNameIsString(ValidStringEnum::class, new stdClass());
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Element name 12467 in enum Zlikavac32\Enum\Tests\Fixtures\ValidStringEnum is not valid
     */
    public function testThatExceptionIsThrownWhenElementNameIsScalar(): void
    {
        assertElementNameIsString(ValidStringEnum::class, 12467);
    }

    public function testThatNoExceptionIsThrownWhenElementIsValidClassInstance(): void
    {
        try {
            assertValidEnumElementObjectType(ValidStringEnum::class, ValidStringEnum::ENUM_A(), ValidStringEnum::class);

            $this->assertTrue(true);
        } catch (Throwable $e) {
            $this->failWithThrowable($e);
        }
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Enum element object in enum Zlikavac32\Enum\Tests\Fixtures\ValidStringEnum must be an instance of Zlikavac32\Enum\Tests\Fixtures\ValidStringEnum (an instance of stdClass received)
     */
    public function testThatExceptionIsThrownWhenElementIsInvalidClassInstance(): void
    {
        assertValidEnumElementObjectType(ValidStringEnum::class, new stdClass(), ValidStringEnum::class);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Enum element object in enum Zlikavac32\Enum\Tests\Fixtures\ValidStringEnum must be an instance of Zlikavac32\Enum\Tests\Fixtures\ValidStringEnum (integer received)
     */
    public function testThatExceptionIsThrownWhenElementIsScalar(): void
    {
        assertValidEnumElementObjectType(ValidStringEnum::class, 12467, ValidStringEnum::class);
    }

    public function testThatNoExceptionIsThrownWhenCollectionIsValid(): void
    {
        try {
            assertValidEnumCollection(
                ValidStringEnum::class,
                ['ENUM_A' => ValidStringEnum::ENUM_A()],
                ValidStringEnum::class
            );

            $this->assertTrue(true);
        } catch (Throwable $e) {
            $this->failWithThrowable($e);
        }
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Element name 0 in enum Zlikavac32\Enum\Tests\Fixtures\ValidStringEnum is not valid
     */
    public function testThatExceptionIsThrownWhenCollectionIsNotValid(): void
    {
        assertValidEnumCollection(ValidStringEnum::class, [new stdClass()], ValidStringEnum::class);
    }

    private function failWithThrowable(Throwable $e): void
    {
        $this->fail(sprintf('No exception was expected but got: %s', $e->getMessage()));
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Enum Zlikavac32\Enum\Tests\Fixtures\EnumThatExtendsValidEnum extends
     *                           Zlikavac32\Enum\Tests\Fixtures\ValidStringEnum which already defines enumerate()
     *                           method
     */
    public function testThatExceptionIsThrownWhenParentHasEnumerate(): void
    {
        assertNoParentHasEnumerateMethodForClass(EnumThatExtendsValidEnum::class);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Class Zlikavac32\Enum\Tests\Fixtures\NonAbstractEnumWithoutEnumerate must be also
     *                           abstract (since
     *                           Zlikavac32\Enum\Tests\Fixtures\EnumThatExtendsNonAbstractEnumWithoutEnumerate extends
     *                           it)
     */
    public function testThatExceptionIsThrownWhenParentIsNotAbstract(): void
    {
        assertNoParentHasEnumerateMethodForClass(EnumThatExtendsNonAbstractEnumWithoutEnumerate::class);
    }

    public function testThatGoodEnumWithParentPasses(): void
    {
        try {
            assertNoParentHasEnumerateMethodForClass(ValidEnumWithOneParent::class);

            $this->assertTrue(true);
        } catch (Throwable $e) {
            $this->failWithThrowable($e);
        }
    }
}
