<?php

namespace OfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserJobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('golongan')->add('jenjangPangkat')->add('jabatan')->add('tanggalMasuk')->add('statusKaryawan')->add('pengalamanKerjaTerakhir')->add('kontrakTraining')->add('kontrakKerja')->add('tanggalPercobaan')->add('tanggalSkTetap')->add('isDeleted')->add('userId');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OfficeBundle\Entity\UserJob',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'officebundle_userjob';
    }
}
