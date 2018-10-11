<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * User controller.
 *
 * @Route("/")
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/api/users", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $users=$this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        if (!count($users)){
            $response=array(
                'code'=>1,
                'message'=>'No posts found!',
                'errors'=>null,
                'result'=>null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $data=$this->get('jms_serializer')->serialize($users,'json');
          $response=array(
            'code'=>0,
             'message'=>'success',
              'errors'=>null,
               'result'=>json_decode($data,true)
);
return new JsonResponse($response,200);
}

    /**
     * @param Request $request
     * @param $id
     * @Route("/api/users/{id}",name="update_user")
     * @Method({"PUT"})
     * @return JsonResponse
     */
    public function updateuser(Request $request,$id)
    {
        $user=$this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user))
        {
            $response=array(
                'code'=>1,
                'message'=>'Post Not found !',
                'errors'=>null,
                'result'=>null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $body=$request->getContent();
        $data=$this->get('jms_serializer')->deserialize($body,'AppBundle\Entity\User','json');
        $user->setRace($data->getRace());
        $user->setAge($data->getAge());
        $user->setFamille($data->getFamille());
        $user->setNourriture($data->getNourriture());
        $em=$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $response=array(
            'code'=>0,
            'message'=>'Post updated!',
            'errors'=>null,
            'result'=>null
        );
        return new JsonResponse($response,200);
    }





    /**
     * Creates a new user entity.
     *
     * @Route("/api/new/{id}", name="user_new")
     * @Method("POST")
     */
    public function newAction(Request $request,$id )
    {

        $data=$request->getContent();
        $user=$this->get('jms_serializer')->deserialize($data,'AppBundle\Entity\User','json');

        $password = $user->getPassword() ;
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
        $em=$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $usero = $em->getRepository('AppBundle:User')->findOneBy(array('id'=> $id));
        $usero->AjoutAmis($user);
        $em->persist($usero);
        $em->flush();
        $response=array(
            'code'=>0,
            'message'=>'Post created!',
            'errors'=>null,
            'result'=>null
        );
        return new JsonResponse($response,Response::HTTP_CREATED);
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('user/show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**


     * @Route("/api/supp/{id}/{ido}", name="Action_supp")
     * @Method({"PUT"})
     */
    public function SuppAmisAction(Request $request,$id ,$ido)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id'=> $ido));
        $amis = $em->getRepository('AppBundle:User')->findOneBy(array('id'=> $id));
        $user->SuppAmis($amis);
        $em->persist($user);
        $em->flush();

        $response = array(
            'code' => 0,
            'message' => 'post deleted !',
            'errors' => null,
            'result' => null
        );
        return new JsonResponse($response, 200);

    }
    /**


     * @Route("/api/users/add/{id}/{ido}", name="Action_ajj")
     * @Method({"PUT"})
     */
    public function AjoutAmisAction(Request $request,$id ,$ido)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id'=> $ido));
        $amis = $em->getRepository('AppBundle:User')->findOneBy(array('id'=> $id));
        $user->AjoutAmis($amis);
        $em->persist($user);
        $em->flush();

        $response = array(
            'code' => 0,
            'message' => 'post deleted !',
            'errors' => null,
            'result' => null
        );
        return new JsonResponse($response, 200);

    }


    /**
     * @Route("/api/users/{id}",name="delete_users")
     * @Method({"DELETE"})
     */
    public function deleteUser($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user)) {
            $response = array(
                'code' => 1,
                'message' => 'post Not found !',
                'errors' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        $response = array(
            'code' => 0,
            'message' => 'post deleted !',
            'errors' => null,
            'result' => null
        );
        return new JsonResponse($response, 200);
    }}
