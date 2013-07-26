<?php

/*
 * This file is part of the RCOpenSSLCrypto package.
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace RC\OpenSSLCryptoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use RC\OpenSSLCryptoBundle\Services\BrowserKeyGenerator;

/**
 * @author Rafael Calleja <rafa.calleja@d-noise.net>
 */
class BrowserCertificatedCommand extends ContainerAwareCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
		->setName('rc:openssl:browser')
		->setDescription('Crea un certificado autofirmado para el navegador')
		->setDefinition(array(
				new InputArgument('pkcs-passphrase', InputArgument::REQUIRED, 'The PKCS passphrase'),
                new InputArgument('pkcs-passphrase2', InputArgument::REQUIRED, 'The PKCS passphrase confirmation'),
                new InputArgument('output', InputArgument::REQUIRED, 'Destination file'),
		))
		->setHelp(<<<EOT
            	<info>
________$$$\$______________________________
_______$$$$$$\$_________________________$$$
________$$$$$$\$_____________________$$$$
_________$$$$$$\$____$\$_____$\$____$$$$$
__________$$$$$$\$_$$$$\$_$$$$\$__$$$$$$$
___________$$$$$$\$_$$$$$$\$_$$$$$$\$_$$$$$$\$_
____________$$$$$\$_$$$$$$\$_$$$$$$$$$$$$\$_
_________$$$$$\$_$$$$$$\$_$$$$$$\$_$$$$$\$_
_$$$$\$____$$$$$\$_$$$$$$\$_$$$$$$\$_$$$$$\$_
$$$$$$$$$$$$$$$\$_$$$$$$\$_$$$$$$\$_$$$$\$_
$$$$$$$$$$$$$$$$$\$_$$$$$$\$_$$$$$$\$_$$$$\$_
___$$$$$$$$$$$$$$\$_$$$$$$\$_$$$$$\$_$$$$$\$_
______$$$$$$$$$$$$\$_$$$$\$__$\$_$$$$$\$_$\$_
_______$$$$$$$$$$$\$___$$$\$_____$$$$\$_
_________$$$$$$$$$$$$$$$$$$$$$$$$$$$$\$_
__________$$$$$$$$$$$$$$$$$$$$$$$$$$\$_
____________$$$$$$$$$$$$$$$$$$$$$$$\$_
_______________$$$$$$$$$$$$$$$$$$$\$_
            		</info>
EOT
		);
	}

	/**
	 * @see Command
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
        $clientpass = $file = $input->getArgument('pkcs-passphrase');;
        $private = $this->getContainer()->getParameter('rc_open_ssl_crypto.privkey_path');
        $cert = $this->getContainer()->getParameter('rc_open_ssl_crypto.cert_path');
        $config = $this->getContainer()->getParameter('rc_open_ssl_crypto.conf_path');
        $pass = $this->getContainer()->getParameter('rc_open_ssl_crypto.passphrase');
        $pkcs = $input->getArgument('output');;

		if(!file_exists($private)){
			$output->writeln(sprintf('<error>Unknonw File: </error> <comment>%s</comment>', $private));
			return false;
		}

        if(!file_exists($cert)){
            $output->writeln(sprintf('<error>Unknonw File:</error> <comment>%s</comment>', $cert));
            return false;
        }

        if(!file_exists($private)){
            $output->writeln(sprintf('<error>Unknonw File:</error> <comment>%s</comment>', $private));
            return false;
        }

        $gen = new BrowserKeyGenerator($pass, $cert, $private, $config);

        if( $error = $gen->generate() !== true ){
            $output->writeln(sprintf('<error>No se ha podido generar el certificado: </error> <comment>%s</comment>', $error));
            return false;
        }


        file_put_contents($pkcs, $gen->getClientCertificate($clientpass) );

		$output->writeln(sprintf('Se ha generado correctamente el certificado'));
        $output->writeln(sprintf('PKCS: <comment>%s</comment>', $pkcs));
	}

	/**
	 * @see Command
	 */
	protected function interact(InputInterface $input, OutputInterface $output)
	{
		if (!$input->getArgument('pkcs-passphrase')) {
			$pass = $this->getHelper('dialog')->askAndValidate(
					$output,
					'<question>Por favor introduzca un clave de exportación:</question>',
					function($pass) {
				if (empty($pass)) {
					throw new \Exception('pkcs-passphrase can not be empty');
				}

				return $pass;
			}
			);
			$input->setArgument('pkcs-passphrase', $pass);
		}

        if (!$input->getArgument('pkcs-passphrase2')) {
            $pass2 = $this->getHelper('dialog')->askAndValidate(
                $output,
                '<question>Por favor introduzca la misma clave de exportación :</question>',
                function($pass2) {
                    if (empty($pass2)) {
                        throw new \Exception('pkcs-passphrase2 can not be empty');
                    }

                    return $pass2;
                }
            );
            $input->setArgument('pkcs-passphrase2', $pass2);
        }


        if ($pass != $pass2) {
            throw new \Exception('las claves no coinciden');
        }

        if (!$input->getArgument('output')) {
            $ot = $this->getHelper('dialog')->askAndValidate(
                $output,
                '<question>Por favor introduzca un archivo de destino:</question>',
                function($ot) {
                    if (empty($ot)) {
                        throw new \Exception('output: can not be empty');
                    }

                    return $ot;
                }
            );
            $input->setArgument('output', $ot);
        }

        if(file_exists($ot)){
            throw new \Exception('El archivo ya existe');
        }

	}
}
