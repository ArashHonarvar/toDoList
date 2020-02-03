<?php


namespace App\Form\Task;


use App\Entity\Task\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('dueDate', DateTimeType::class , ['widget' => 'single_text'])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    "Ready" => Task::STATUS_READY,
                    "Doing" => Task::STATUS_DOING,
                    "Done" => Task::STATUS_DONE,
                    "Expired" => Task::STATUS_EXPIRED,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'csrf_protection' => false
        ]);
    }

}