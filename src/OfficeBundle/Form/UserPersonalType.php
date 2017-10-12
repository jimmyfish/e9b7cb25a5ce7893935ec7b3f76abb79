<?php

namespace OfficeBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPersonalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('noRegistrasi', TextType::class, array(
                'data' => 'reg1234',
                'label' => 'Nomer Registrasi',
            ))
            ->add('nik', TextType::class, array('data' => 'nik1234'))
            ->add('username')
            ->add('nama', TextType::class, array('label' => 'Nama', 'attr' => array('class' => 'text-center')))
            ->add('email')
            ->add('password')
            ->add('tempatLahir')
            ->add('tanggalLahir', TextType::class, array(
                'label' => 'Tanggal Lahir',
                'attr' => array('class' => 'form-control time-picker'),
            ))
            ->add('tanggalMasuk')
            ->add('tanggalPensiun')
            ->add('jenisKelamin', ChoiceType::class, array(
                'choices' => array(
                    0 => 'Male',
                    1 => 'Female',
                ),
            ))
            ->add('tempatTinggal')
            ->add('alamatSurat')
            ->add('golonganDarah')
            ->add('noKtp')
            ->add('agama')
            ->add('kebangsaan')
            ->add('pendidikan')
            ->add('asalSekolah')
            ->add('jurusan')
            ->add('bpjs')
            ->add('npwp')
            ->add('noTelp')
            ->add('ukuranPakaian')
            ->add('profilePicture')
            ->add('status')
            ->add('role')
            ->add('isValidated', HiddenType::class, array('data' => 0))
            ->add('isDeleted', HiddenType::class, array('data' => 0))
            ->add('penempatan', EntityType::class, array(
                'class' => 'OfficeBundle:CompanyProfile',
                'choice_value' => 'id',
                'choice_label' => 'nama_perusahaan',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OfficeBundle\Entity\UserPersonal',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'officebundle_userpersonal';
    }
}
