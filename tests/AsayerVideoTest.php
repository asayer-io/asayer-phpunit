<?php
namespace Client;

require_once __DIR__ . '/../vendor/autoload.php';

use Asayer\AsayerTest;

class AsayerVideoTest extends AsayerTest{


    protected $url = 'https://asayer.io';

    public function testAsayerVideo()
    {
        $this->webDriver->get($this->url);
	

	$playVideoButton = $this->webDriver->findElement(\WebDriverBy::cssSelector('body > div.wrapper > div.container-fluid.hero > div > div.col-md-5.hero-copy.heading-spacer > button'));
	$playVideoButton->click();
$video = $this->webDriver->findElement(\WebDriverBy::tagName('video'));
$this->webDriver->wait(10, 1000)->until(
  \WebDriverExpectedCondition::visibilityOf($video)
);

        $this->assertContains('Asayer', $this->webDriver->getTitle());
    }    

}
?>
