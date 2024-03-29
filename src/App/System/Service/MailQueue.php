<?php

namespace Be\Sf\App\System\Service;

use Be\F\App\ServiceException;
use Be\Sf\Be;


/**
 * 通过队列发送邮件
 *
 * Class MailQueue
 * @package Be\Mf\App\System\Service
 */
class MailQueue
{

    /**
     * 发送邮件
     *
     * @param string | array $to 收件人
     * @param string $subject 主题
     * @param string $body 内容
     * @param string | array | null $cc
     * @param string | array | null $bcc
     * @throws \Exception
     */
    public function send($to, $subject = '', $body = '', $cc = null, $bcc = null)
    {
        $data = new \stdClass();

        $data->to_email = '';
        $data->to_name = '';
        if (is_string($to)) {
            $data->to_email = $to;
        } else {
            if (is_array($to)) {
                if (isset($to['email'])) {
                    $data->to_email = $to['email'];
                }

                if (isset($to['name'])) {
                    $data->to_name = $to['name'];
                }
            }
        }

        if (!$data->to_email) {
            // 收件人邮箱缺失！
            throw new ServiceException('Receiver email is missing!');
        }

        $data->subject = $subject;
        $data->body = $body;

        $data->cc_email = '';
        $data->cc_name = '';
        if ($cc !== null) {
            if (is_string($cc)) {
                $data->cc_email = $cc;
            } else {
                if (is_array($cc)) {
                    if (isset($cc['email'])) {
                        $data->cc_email = $cc['email'];

                        if (isset($cc['name'])) {
                            $data->cc_name = $cc['name'];
                        }
                    }
                }
            }
        }

        $data->bcc_email = '';
        $data->bcc_name = '';
        if ($bcc !== null) {
            if (is_string($bcc)) {
                $data->bcc_email = $bcc;
            } else {
                if (is_array($bcc)) {
                    if (isset($bcc['email'])) {
                        $data->bcc_email = $bcc['email'];

                        if (isset($bcc['name'])) {
                            $data->bcc_name = $bcc['name'];
                        }
                    }
                }
            }
        }

        $db = Be::getDb();
        $db->insert('system_mail_queue', $data);
    }

}
