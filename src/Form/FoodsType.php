<?php

namespace App\Form;

use App\Entity\Food;
use App\Entity\FoodTags;
use App\Entity\Tags;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Intl\Countries;

class FoodsType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('origin', ChoiceType::class, [
                'label' => 'Origine',
                'required' => true,
                'choices' => array_flip(Countries::getNames()),
                'choice_translation_domain' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('foodTags', EntityType::class, [
                'class' => Tags::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
                'label' => 'Tags',
            ]);

        //Pour enregistrer les tags sélectionnés dans la base de données
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $food = $event->getData();
            $form = $event->getForm();
            $selectedTags = $form->get('foodTags')->getData();

            foreach ($selectedTags as $tag) {
                $foodTag = new FoodTags();
                $foodTag->setFood($food);
                $foodTag->setTag($tag);
                $this->entityManager->persist($foodTag);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Food::class,
        ]);
    }
}
