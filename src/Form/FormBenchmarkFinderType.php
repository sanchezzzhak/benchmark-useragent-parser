<?php

namespace App\Form;

use App\Entity\BenchmarkResult;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormBenchmarkFinderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_agent', TextType::class, [
                'required' => false,
            ])
            ->add('send', SubmitType::class)
        ;
    }


}
