<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ParserRunForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('source', TextType::class, [
                'label' => 'Источник',
            ])
            ->add('count', NumberType::class, [
                'label' => 'Количество новостей',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Запуск',
            ]);
    }
}
