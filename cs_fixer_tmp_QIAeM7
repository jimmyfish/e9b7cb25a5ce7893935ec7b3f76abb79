<?php

namespace OfficeBundle\Form;

use SC\DatetimepickerBundle\Form\Type\DatetimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShiftType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', TextType::class, [
            'label' => 'Keterangan Shift',
        ])
        // ->add('startTime', DatetimeType::class, [
        //     'label' => 'Jam Masuk',
        //     'pickerOptions' => array(
        //         'format' => 'hh:ii p',
        //         'weekstart' => 0,
        //         'todayBtn' => true,
        //         'todayHighlight' => true,
        //         'maxView' => 0,
        //         'autoclose' => true,
        //     ),
        // ])
        ->add('startTime', TimeType::class, [
            'input' => 'timestamp',
            'widget' => 'choice',
            'attr' => [
                'class' => 'form-control',
            ],
        ])
        ->add('endTime', DatetimeType::class, [
            'label' => 'Jam Pulang',
            'pickerOptions' => array(
                'format' => 'LT',
                'weekstart' => 0,
                'todayBtn' => true,
                'todayHighlight' => true,
                'minView' => 2,
                'autoclose' => true,
            ),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OfficeBundle\Entity\Shift',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'officebundle_shift';
    }
}
