<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AppBundle\Entity\Event;

/**
 * 
 */
class CallEventFormType extends AbstractType
{
	
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
            ->add('name')
            ->add('eventDate')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Call' => 'Call'
                ]
            ])
            ->add('participants', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class
        ]);
    }
}