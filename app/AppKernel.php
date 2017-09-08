<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Avanzu\AdminThemeBundle\AvanzuAdminThemeBundle(),
            new AdminBundle\AdminBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new SalexUserBundle\SalexUserBundle(), //Salex user management bundle
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(), //doctrine migration bundle
            new Liip\ImagineBundle\LiipImagineBundle(), //image manipulation bundle
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(), //KNP menu bundle
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(), //Doctrine extension bundle
            new Vich\UploaderBundle\VichUploaderBundle(), //file uploader bundle
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(), //CK Editor
            new WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(), //breadcrumbs

        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
