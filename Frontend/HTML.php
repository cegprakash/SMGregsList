<?php
namespace SMGregsList\Frontend;
use SMGregsList\Messager, SMGregsList\Frontend, SMGregsList\SearchPlayer, SMGregsList\Player, SMGregsList\SellPlayer, SMGregsList\SavedSearches,
    SMGregsList\Manager;
class HTML extends Messager implements Frontend
{
    protected $template;
    protected $searchresults = array();
    protected $savedSearches;
    protected $searchfor;
    protected $verifyphase = false;
    protected $confirmphase = false;
    protected $deleted = false;
    protected $body;
    protected $manager = false;
    protected $code = false;
    protected $extrarender;

    function __construct(HTMLController $controller = null)
    {
        if (null === $controller) {
            $controller = new HTMLController;
        }
        $this->searchfor = new SearchPlayer;
        $this->sellplayer = new SellPlayer;
        $this->addDependency($controller);
        $this->template = new \PEAR2\Templates\Savant\Main(array(
            'template_path' => realpath(__DIR__ . '/../templates'),
            'escape' => 'htmlentities',
        ));
        $this->manager = filter_input(INPUT_COOKIE, 'manager', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->code = filter_input(INPUT_COOKIE, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    function getBody()
    {
        return $this->body;
    }

    function getManager()
    {
        return $this->manager;
    }

    function getCode()
    {
        return $this->code;
    }

    function getExtraRender()
    {
        return $this->extrarender;
    }

    function listMessages(array $newmessages)
    {
        return parent::listMessages(array_merge($newmessages, array('ready', 'searchResult', 'search', 'playerAdded', 'sellDetected',
                                          'verify', 'confirm', 'playerRemoved', 'retrieveCookie', 'searchSaved', 'savedSearches')));
    }

    function receive($message, $content)
    {
        if ($message == 'ready') {
            // display html output
            $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
            if (strpos($phpSelf, 'sell.php')) {
                $this->discoverSell();
                $this->setupSellPage();
                $this->subtitle = 'List a Player for sale';
            } else {
                $this->discoverSearch();
                $this->discoverSavedSearches();
                $this->body = new SearchResults($this->savedSearches, $this->searchfor, $this->searchresults, $this->manager, $this->code);
                $this->subtitle = 'Search for Players for sale';
                if (count($_GET)) {
                    $this->extrarender = "<script type=\"text/javascript\">
\$(document).ready(function() 
    { 
        \$('#searchresultstable').tablesorter(); 
    } 
);
\$('#myModal').modal('toggle');
    </script>";
                }
            }
            echo $this->template->render($this);
        } elseif ($message == 'searchResult') {
            if (!is_array($content)) {
                throw new \Exception('internal Error: results of search is not an array');
            }
            foreach ($content as $player) {
                if (!($player instanceof Player)) {
                    throw new \Exception('internal Error: one of the results of search is not a Player object');
                }
            }
            $this->searchresults = $content;
        } elseif ($message == 'search') {
            $this->searchfor = $content;
        } elseif ($message == 'playerAdded' || $message == 'sellDetected') {
            $this->sellplayer = $content;
            if ($message == 'playerAdded') {
                $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
                setcookie('manager', $content->getManager()->getName(), strtotime("+365 days"), '/sm', $domain, false, false);
                setcookie('code', $content->getManager()->getCode(), strtotime("+365 days"), '/sm', $domain, false, false);
            }
        } elseif ($message == 'verify') {
            $this->verifyphase = true;
            $this->confirmphase = false;
        } elseif ($message == 'playerRemoved') {
            $this->sellplayer = $content;
            $this->deleted = true;
        } elseif ($message == 'confirm') {
            $this->confirmphase = true;
            $this->verifyphase = false;
        } elseif ($message == 'retrieveCookie') {
            if ($this->manager) {
                $this->broadcast('cookieRetrieved', array('manager' => $this->manager, 'code' => $this->code));
            }
        }
    }

    function setupSellPage()
    {
        if ($this->verifyphase) {
            $this->body = new Verify($this->sellplayer);
        } elseif ($this->confirmphase) {
            $this->body = new Listed($this->sellplayer);
        } elseif ($this->deleted) {
            $this->body = new Deleted($this->sellplayer);
        } else {
            $this->body = new Sell($this->sellplayer);
        }
    }

    function discoverSearch()
    {
        $this->searchresults = array();
        $this->broadcast('detectSearch');
    }

    function discoverSavedSearches()
    {
        $manager = new Manager();
        if ($this->manager) {
            $manager->name = $this->manager;
            $manager->code = $this->code;
        }
        $this->savedSearches = $this->ask('getAllSavedSearches', $manager);
        if (!$this->savedSearches) {
            $this->savedSearches = new SavedSearches(array());
        }
    }

    function discoverSell()
    {
        $this->broadcast('detectSell');
    }
}