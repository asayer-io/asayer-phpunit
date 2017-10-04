<?php
namespace Client;

require_once __DIR__ . '/../vendor/autoload.php';

use Asayer\AsayerWebDriver;

class MyTest extends AsayerWebDriver
{
    public function testProductPage()
    {
        $this->webDriver->get('https://asayer.io');

        $productButton = $this->webDriver->findElement(\WebDriverBy::cssSelector('a[href="product.html"]'));
        $productButton->click();

        $this->assertContains( 'Asayer', $this->webDriver->getTitle());
    }
}

?>
