<?php

namespace Optipic\Optipic\Model;

use Optipic\Optipic\Helper\Data;
use Optipic\Optipic\Helper\ImgUrlConverter;

class Optipic
{
    private $dataHelper;

    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    public function changeContent($content) {

        $settings = $this->getSettings();
        $dir = $this->getCurrentUrlDir();

        if($settings['autoreplace_active'] && $settings['site_id']) {
            $converterOptiPic = new ImgUrlConverter($settings);
            $content = $converterOptiPic->convertHtml($content);
        }

        return $content;
    }

    public function getSettings() {

        $optipicSiteID = $this->dataHelper->getGeneralConfig('optipic_site_id');
        $autoreplaceActive = $this->dataHelper->getGeneralConfig('optipic_autoreplace_active');
        $domainsOrig = $this->dataHelper->getGeneralConfig('optipic_domains');
        $cdnDomain = $this->dataHelper->getGeneralConfig('optipic_cdn_domain');
        
        $textareaSettings = array(
            'optipic_domains' => $this->dataHelper->getGeneralConfig('optipic_domains'),
            'optipic_exclusions_url' => $this->dataHelper->getGeneralConfig('optipic_exclusions_url'),
            'optipic_whitelist_img_urls' => $this->dataHelper->getGeneralConfig('optipic_whitelist_img_urls'),
            'optipic_srcset_attrs' => $this->dataHelper->getGeneralConfig('optipic_srcset_attrs'),
        );
        
        foreach($textareaSettings as $setKey=>$setValue) {
            $list = array();
            foreach(explode("\n",$setValue) as $row) {
                $row = trim($row);
                if($row) {
                    $list[] = $row;
                }
            }
            $textareaSettings[$setKey] = $list;
        }

        return array(
            'site_id' => $optipicSiteID,
            'autoreplace_active' => ($autoreplaceActive=='1'),
            'domains' => $textareaSettings['optipic_domains'],
            'exclusions_url' => $textareaSettings['optipic_exclusions_url'],
            'whitelist_img_urls' => $textareaSettings['optipic_whitelist_img_urls'],
            'srcset_attrs' => $textareaSettings['optipic_srcset_attrs'],
            'cdn_domain' => $cdnDomain,
        );
    }

    public function buildImgUrl($localUrl, $params=array()) {
        $dir = $this->getCurrentUrlDir();

        $schema = "//";

        if(!isset($params['site_id'])) {
            $settings = $this->getSettings();
            $params['site_id'] = $settings['site_id'];
        }

        if(isset($params['url_schema'])) {
            if($params['url_schema']=='http') {
                $schema = "http://";
            }
            elseif($params['url_schema']=='https') {
                $schema = "https://";
            }
        }


        if($params['site_id']) {
            if(!strlen(trim($localUrl)) || stripos($localUrl, 'cdn.optipic.io')!==false) {
                return $localUrl;
            }
            /*elseif(stripos($localUrl, 'http://')===0) {
                return $localUrl;
            }
            elseif(stripos($localUrl, 'https://')===0) {
                return $localUrl;
            }
            elseif(stripos($localUrl, '//')===0) {
                return $localUrl;
            }*/
            else {
                $siteUrlLength = strlen($_SERVER['SERVER_NAME']);

                // убираем адрес сайта из начала URL (для http)
                if(stripos($localUrl,'http')===0) {
                    //$protocol = defined('HTTPS') ? 'https' : 'http';
                    $localUrl = substr($localUrl, $siteUrlLength + 7);
                }
                // убираем адрес сайта из начала URL (для https)
                elseif(stripos($localUrl, 'https')===0) {
                    //$protocol = defined('HTTPS') ? 'https' : 'http';
                    $localUrl = substr($localUrl, $siteUrlLength + 8);
                }

                // если URL не абсолютный - приводим его к абсолютному
                if(substr($localUrl, 0, 1)!='/') {
                    $localUrl = $dir.$localUrl;
                }

                $url = $schema.'cdn.optipic.io/site-'.$params['site_id'];
                if(isset($params['q'])) {
                    $url .= '/optipic-q='.$params['q'];
                }
                if(isset($params['maxw'])) {
                    $url .= '/optipic-maxw='.$params['maxw'];
                }
                if(isset($params['maxh'])) {
                    $url .= '/optipic-maxh='.$params['maxh'];
                }

                $url .= $localUrl;

                return $url;

                //return '<img'.$matches[1].'src='.$quoteSymbol.'//cdn.optipic.io/site-'.$settings['site_id'].$url.$quoteSymbol.$matches[3].'>';
            }
        }
        // Если URL
        else {
            return $localUrl;
        }


    }

    public function getCurrentUrlDir() {
        return substr($_SERVER['REQUEST_URI'], 0, strripos($_SERVER['REQUEST_URI'], '/')+1);
    }


}