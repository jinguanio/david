<?php
class DependencyAndDataProviderComboTest extends PHPUnit_Framework_TestCase
{
    public function provider()
    {
        return array(array('provider1'), array('provider2'));
    }

    public function testProducerFirst()
    {
        $this->assertTrue(true);
        return 'first';
    }

    public function testProducerSecond()
    {
        $this->assertTrue(true);
        return 'second';
    }

    /**
     * @depends testProducerFirst
     * @depends testProducerSecond
     * @dataProvider provider
     */
    public function testConsumer()
    {
        $args = func_get_args();

        if ($args[0] === 'provider1') {
            $this->assertEquals(
                array('provider1', 'first', 'second'),
                func_get_args()
            );
        } else {
            $this->assertEquals(
                array('provider2', 'first', 'second'),
                func_get_args()
            );
        }
    }
}
