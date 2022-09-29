<?php
/**
 *  2007-2022 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 202-ecommerce <tech@202-ecommerce.com>
 *  @copyright Copyright (c) 2007-2022 202-ecommerce
 *  @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 */
class EbayLogger
{
    const DEBUG = 0;
    const INFO = 2;
    const WARNING = 5;
    const ERROR = 6;
    const FATAL = 9;

    private static $loggers = [];

    public static $severity = [
        'DEBUG' => self::DEBUG,
        'INFO' => self::INFO,
        'WARNING' => self::WARNING,
        'ERROR' => self::ERROR,
        'FATAL' => self::FATAL,
    ];

    protected $level;

    protected $uid;

    protected $context;

    public function __construct($level = 'INFO', $context = null, $uid = '')
    {
        $this->level = self::$severity[Tools::strtoupper($level)];
        $this->uid = uniqid((string) $uid, true);
        $this->context = $context;
    }

    public static function install()
    {
        $sql = [];
        // Create ebay_logs Table in Database
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ebay_logs` (
            `id_ebay_logs` INT(16) NOT NULL AUTO_INCREMENT,
            `datetime` DATETIME NOT NULL,
            `severity` TINYINT(1) NOT NULL DEFAULT 0,
            `code` INT(11) NOT NULL DEFAULT 0,
            `message` TEXT,
            `context` TEXT,
            `backtrace` TEXT,
            `uid` TEXT,
            PRIMARY KEY (`id_ebay_logs`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        foreach ($sql as $q) {
            Db::getInstance()->Execute($q);
        }
    }

    public static function get($name = null, $level = null, $context = null)
    {
        if ($name == null) {
            // on recupere la derniere instance crée
            $name = self::$loggers ? key(array_slice(self::$loggers, -1, 1, true)) : '';
        }
        if (!array_key_exists($name, self::$loggers)) {
            self::$loggers[$name] = new EbayLogger($level, $context, $name);
        }

        return self::$loggers[$name];
    }

    public static function getLogs($uid = null, $keyword = null, $limit = 200)
    {
        $levels = array_flip(self::$severity);
        $sql = '
			SELECT * FROM ' . _DB_PREFIX_ . 'ebay_logs
		';
        $where = [];
        if ($keyword) {
            $where[] = ' message LIKE "%' . pSQL($keyword) . '%" OR context LIKE "%' . pSQL($keyword) . '%"';
        }
        if ($uid) {
            $where[] = 'uid = "' . pSQL($uid) . '" ';
        }
        if ($where) {
            $sql .= 'WHERE ' . implode(' AND ', $where);
        }

        $sql .= 'ORDER BY id_ebay_logs ASC ';
        if ($limit) {
            $sql .= 'LIMIT ' . (int) $limit;
        }

        $rows = Db::getInstance()->ExecuteS($sql);
        foreach ($rows as &$row) {
            $row['severity_label'] = $levels[(int) $row['severity']];
        }

        return $rows;
    }

    public static function clear()
    {
        return Db::getInstance()->Execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_logs');
    }

    public static function export($uid = null)
    {
        $logs = self::getLogs($uid);

        $csvcontent = fopen('php://output', 'w');
        fputcsv($csvcontent, array_keys($logs[0]));
        foreach ($logs as $row) {
            fputcsv($csvcontent, array_values($row));
        }
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=logs.csv');
    }

    public static function display()
    {
        $currentIndex = 'index.php?controller=AdminModules&configure=ebay&&token=' . Tools::getAdminTokenLite('AdminModules');

        $action = Tools::getValue('logs');
        $keyword = Tools::getValue('keyword');
        $uid = Tools::getValue('uid');

        if ($action == 'CLEAR') {
            self::clear();
        } else {
            if ($action == 'TEST') {
                self::clear();
                self::test();
            } else {
                if ($action == 'EXPORT') {
                    return self::export();
                }
            }
        }

        $logs = self::getLogs($uid, $keyword); //self::getLogs();

        $fields_list = [
            'datetime' => [
                'title' => 'Datetime',
                'width' => 140,
                'type' => 'text',
            ],

            'severity_label' => [
                'title' => 'Severity',
                'width' => 140,
                'type' => 'text',
            ],

            'message' => [
                'title' => 'Message',
                'width' => 140,
                'type' => 'text',
            ],
            'context' => [
                'title' => 'Context',
                'width' => 140,
                'type' => 'text',
            ],
            'uid' => [
                'title' => 'Operation',
                'width' => 140,
                'type' => 'hidden',
            ],
        ];
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = ['view'];
        $helper->identifier = 'uid';
        $helper->show_toolbar = true;
        $helper->title = 'logs ebay';
        $helper->table = '_ebay_logs';
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        //$currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&totlookbook_edit='.Tools::getValue('totlookbook_edit', 'global');
        $helper->currentIndex = $_SERVER['REQUEST_URI'];

        $html = '';
        $html .= $helper->generateList($logs, $fields_list);

        return $html;
    }

    public static function test()
    {
        $logger1 = new EbayLogger('DEBUG');
        $logger2 = new EbayLogger('INFO');
        $logger3 = new EbayLogger('WARNING');
        $logger4 = new EbayLogger('INFO', [
            'user' => 'testuser',
            'shop' => 'shop',
            'operation' => 'sync product',
            'id_product' => 123,
        ]);

        $logger1->debug('logger1 debug');
        $logger1->info('logger1 info');
        $logger1->warning('logger1 warning');
        $logger1->error('logger1 error');

        $logger2->debug('logger2 debug');
        $logger2->info('logger2 info');
        $logger2->warning('logger2 warning');
        $logger2->error('logger2 error');

        $logger3->debug('logger3 debug');
        $logger3->info('logger3 info');
        $logger3->warning('logger3 warning');
        $logger3->error('logger3 error');

        $logger4->info('start sync');
        $logger4->info('start sync variation1', ['id_variation' => 1]);
        $logger4->info('start sync variation2', ['id_variation' => 2]);
        $logger4->info('start sync variation3', ['id_variation' => 3]);

        // test recuperation d'un logger par nom
        $log = EbayLogger::get('MYLOGGER', 'INFO', ['loggerused' => 'MYLOGGER']);
        $log->info('test MYLOGGER');

        $log = EbayLogger::get('MYLOGGER'); // doit etre la même instance
        $log->info('test MYLOGGER');

        $log = EbayLogger::get(); // doit etre la même instance (si !name on prend la derniere instance créée (curent instance))
        $log->info('test MYLOGGER');
    }

    private function log($severity, $msg, $context = null, $backtrace = null)
    {
        if ($severity >= $this->level) {
            $datetime = date('Y-m-d H:i:s'); // Microseconds ???

            $ctx = array_merge((array) $this->context, (array) $context);

            if (method_exists('MySQL', 'insert')) {
                Db::getInstance()->insert(
                    'ebay_logs',
                    [
                        'uid' => $this->uid,
                        'datetime' => $datetime,
                        'severity' => (int) $severity,
                        'code' => 0,
                        'message' => pSQL($msg),
                        'context' => $ctx ? pSQL(Tools::jsonEncode($ctx)) : null,
                        'backtrace' => $backtrace,
                    ]
                );
            } else {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'ebay_logs(`uid`, `datetime`, `severity`, `code`, `message`, `context`, `backtrace`)
			VALUES(\'' . (int) $this->uid . '\', \'' . $datetime . '\', \'' . (int) $severity . '\', \'0\', \'' . pSQL($msg) . '\',
			 \'' . pSQL($ctx ? Tools::jsonEncode($ctx) : null) . '\', \'' . pSQL($backtrace) . '\')';

                Db::getInstance()->Execute($sql);
            }
        }
    }

    public function debug($msg, $context = null)
    {
        $this->log(self::DEBUG, $msg, $context);
    }

    public function info($msg, $context = null)
    {
        $this->log(self::INFO, $msg, $context);
    }

    public function warning($msg, $context = null)
    {
        $this->log(self::WARNING, $msg, $context);
    }

    public function error($msg, $context = null, $backtrace = null)
    {
        $this->log(self::ERROR, $msg, $context, $backtrace);
    }

    public function fatal($msg, $context = null, $backtrace = null)
    {
        //$backtrace = debug_backtrace();
        //$file = $backtrace[0]['file'] . ":" . $backtrace[0]['line'];
        $this->log(self::FATAL, $msg, $context, $backtrace);
    }
}
