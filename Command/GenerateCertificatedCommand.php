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


/**
 * @author Rafael Calleja <rafa.calleja@d-noise.net>
 */
class GenerateCertificatedCommand extends ContainerAwareCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
		->setName('rc:openssl:generate_csr')
		->setDescription('Crea un certificado autofirmado')
		/*->setDefinition(array(
				new InputArgument('file', InputArgument::REQUIRED, 'The fixture file'),
		))*/
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
        $private = $this->getContainer()->getParameter('rc_open_ssl_crypto.privkey_path');
        $public = $this->getContainer()->getParameter('rc_open_ssl_crypto.pubkey_path');
        $csr = $this->getContainer()->getParameter('rc_open_ssl_crypto.csr_path');
        $pkcs = $this->getContainer()->getParameter('rc_open_ssl_crypto.client_path');

		if(file_exists($private)){
			$output->writeln(sprintf('<error>File Exists, delete it by hand: </error> <comment>%s</comment>', $private));
			return false;
		}

        if(file_exists($public)){
            $output->writeln(sprintf('<error>File Exists, delete it by hand: </error> <comment>%s</comment>', $public));
            return false;
        }

        if(file_exists($csr)){
            $output->writeln(sprintf('<error>File Exists, delete it by hand: </error> <comment>%s</comment>', $csr));
            return false;
        }

        if(file_exists($pkcs)){
            $output->writeln(sprintf('<error>File Exists, delete it by hand: </error> <comment>%s</comment>', $pkcs));
            return false;
        }

        $gen = $this->getContainer()->get('rc.keygenerator');

        if( $error = $gen->generate() !== true ){
            $output->writeln(sprintf('<error>No se ha podido generar el certificado: </error> <comment>%s</comment>', $error));
            return false;
        }


        file_put_contents($private, $gen->getPrivatekey() );
        file_put_contents($public, $gen->getPublickey() );
        file_put_contents($csr, $gen->getCsr() );
        file_put_contents($pkcs, $gen->getClientCertificate() );

		$output->writeln(sprintf('Se ha generado correctamente el certificado'));
        $output->writeln(sprintf('Private: <comment>%s</comment>', $private));
        $output->writeln(sprintf('Public: <comment>%s</comment>', $public));
        $output->writeln(sprintf('CSR: <comment>%s</comment>', $csr));
        $output->writeln(sprintf('PKCS: <comment>%s</comment>', $pkcs));
	}

	/**
	 * @see Command
	 */
	protected function interact(InputInterface $input, OutputInterface $output)
	{
		/*if (!$input->getArgument('file')) {
			$file = $this->getHelper('dialog')->askAndValidate(
					$output,
					'<question>Por favor selecciona un archivo de fixture para recargar:</question>',
					function($file) {
				if (empty($file)) {
					throw new \Exception('file can not be empty');
				}

				return $file;
			}
			);
			$input->setArgument('file', $file);
		}*/

	}
}
