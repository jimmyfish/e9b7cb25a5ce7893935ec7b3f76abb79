<?php

namespace OfficeBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OfficeBundle\Entity\Attachment;
use OfficeBundle\Entity\CompanyProfile;
use OfficeBundle\Entity\Holiday;
use OfficeBundle\Entity\ImageResize;
use OfficeBundle\Entity\Shift;
use OfficeBundle\Entity\UserFamily;
use OfficeBundle\Entity\UserJob;
use OfficeBundle\Entity\UserPersonal;
use OfficeBundle\Entity\Cuti;
use OfficeBundle\Entity\UserPresence;
use OfficeBundle\Form\UserPersonalType;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Util\Debug;

class UserController extends Controller
{

    public function successIzinAction()
    {
        return $this->render('OfficeBundle:user:success-izin.html.twig');
    }

    public function createAction(Request $request)
    {
        //        if ($request->getMethod() == 'GET') {
//            return $this->render('');
//        }
//
//        if ($request->getMethod() == 'POST') {
//            $em = $this->getDoctrine()->getManager();
//
//            $user = new UserPersonal();
//
//            $test = $user->createUser($request);
//
//            $em->persist($test);
//            $em->flush();
//
//            return $this->redirect('/list-user');
//        }

        $form = $this->createForm(UserPersonalType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //            $em = $this->getDoctrine()->getManager();
        }

        return $this->render('OfficeBundle:user:create.html.twig', array('form' => $form->createView()));
    }

    public function viewAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $em->getRepository(UserPersonal::class)->findById($user->getId());

        return $this->render('OfficeBundle:user:list.html.twig', array('data' => $data));
    }

    /**
     * This is unused function
     * But don't delete this shit
     * Because maybe someday we need dis'.
     */
    public function loginAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $username = $request->get('username');
            $password = md5($request->get('password'));
            $em = $this->getDoctrine()->getManager();

            $data = $em->getRepository(UserPersonal::class)->findByUsername($username);

            if ($data instanceof UserPersonal) {
                if ($data != null) {
                    if ($password == $data->getPassword()) {
                        $session = $request->getSession();

                        $session->set('uid', ['value' => $data->getId()]);
                        $session->set('username', ['value' => $username]);
                        $session->set('nama', ['value' => $data->getNama()]);
                        $session->set('role', ['value' => $data->getRole()]);

                        if ($data->getRole() == 0) {
                            return $this->redirect($this->generateUrl('office_admin_index'));
                        } else {
                            return $this->redirect($this->generateUrl('office_user_homepage'));
                        }
                    } else {
                        return $this->redirect($this->generateUrl('office_login'));
                    }
                } else {
                    return $this->redirect($this->generateUrl('office_login'));
                }
            }
        }

        return $this->render('OfficeBundle:Default:login.html.twig');
    }

    public function homeAction(Request $request)
    {
        //        if($request->getSession()->get('role') == null){
//            return $this->redirect($this->generateUrl('office_login'));
//        }

        return $this->render('OfficeBundle:home:index.html.twig');
    }

    public function indexAction(Request $request)
    {
        //        if($request->getSession()->get('role') == null){
//            return $this->redirect($this->generateUrl('office_login'));
//        }

        return $this->render('OfficeBundle:home:index.html.twig');
    }

    public function logoutAction(Request $request)
    {
        $session = $request->getSession();
        $session->clear();

        return $this->redirect($this->generateUrl('office_login'));
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(UserPersonal::class)->findById($id);

        $em->remove($data);
        $em->flush();

        return $this->redirect($this->generateUrl('office_user_list'));
    }

    public function registerAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $user = new UserPersonal();

            $dataUser = $user->createUser($request);
            $dataUser->setRoles(serialize(['ROLE_USER']));
            $dataUser->setIsActive(1);

            $em = $this->getDoctrine()->getManager();

            $em->persist($dataUser);
            $em->flush();

            $data = new UserFamily();

            $data->setUserId($em->getRepository(UserPersonal::class)->find($dataUser->getId()));
            $data->setIsDeleted(0);

            $em->persist($data);

            $dataJob = new UserJob();

            $dataJob->setUserId($em->getRepository(UserPersonal::class)->find($dataUser->getId()));
            $dataJob->setIsDeleted(0);

            $em->persist($dataJob);
            $em->flush();

            return $this->redirect($this->generateUrl('office_login'));
        }

        return $this->render('OfficeBundle:Default:register.html.twig');
    }

    public function updateUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $penempatan = $em->getRepository(CompanyProfile::class)->findAll();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $em->getRepository(UserPersonal::class)->findById($user->getId());

        $job = $em->getRepository(UserJob::class)->findByUserId($user->getId());

        $shift = $em->getRepository(Shift::class)->findAll();

        if ($data->getIsValidated() == 0) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'hubungin admin terlebih dahulu untuk memvalidasi status'
            );

            return $this->redirect($this->generateUrl('office_user_list'));
        }

        if ($request->getMethod() == 'POST') {
            if ($data instanceof UserPersonal) {
                $data->setTempatLahir($request->get('tempat_lahir'));
                $data->setTempatTinggal($request->get('tempat_tinggal'));
                $data->setPenempatan($em->getRepository(CompanyProfile::class)->find($request->get('penempatan')));
                $data->setNoTelp(json_encode($request->get('no_telp')));
                $data->setTanggalLahir($request->get('tanggal_lahir'));

                $job->setJabatan($request->get('jabatan'));
                $job->setShift($em->getRepository(Shift::class)->find($request->get('shift')));
                $job->setTanggalMasuk($request->get('tanggal_masuk'));

                $em->persist($job);
                if ($data->getStatus() > 0) {
                    $data->setEmail($request->get('email'));
                    $data->setJenisKelamin($request->get('jenis_kelamin'));
                    $data->setAlamatSurat($request->get('alamat_surat'));
                    $data->setGolonganDarah($request->get('golongan_darah'));
                    $data->setNoKtp($request->get('no_ktp'));
                    $data->setAgama($request->get('agama'));
                    $data->setKebangsaan($request->get('kebangsaan'));
                    $data->setPendidikan($request->get('pendidikan'));
                    $data->setAsalSekolah($request->get('asal_sekolah'));
                    $data->setJurusan($request->get('jurusan'));
                    $data->setBpjs(json_encode($request->get('bpjs')));
                    $data->setNpwp($request->get('npwp'));
                    $data->setUkuranPakaian(json_encode($request->get('ukuran_pakaian')));

                    if (!is_dir($this->getParameter('profile_directory')['resource'])) {
                        @mkdir($this->getParameter('profile_directory')['resource'], 0777, true);
                    }

                    if (!empty($request->files->get('profile_picture'))) {
                        $file = $request->files->get('profile_picture');

                        if ($file != null) {
                            $filename = md5(uniqid()).'.'.$file->guessExtension();

                            $exAllowed = array('jpg', 'png', 'jpeg');

                            $ext = pathinfo($filename, PATHINFO_EXTENSION);

                            if (in_array($ext, $exAllowed)) {
                                if ($file instanceof UploadedFile) {
                                    if (!($file->getClientSize() > (1024 * 1024 * 1))) {
                                        ImageResize::createFromFile(
                                            $request->files->get('profile_picture')->getPathName()
                                        )->saveTo($this->getParameter('profile_directory')['resource'].'/'.$filename, 20, true);
                                        $data->setProfilePicture($filename);
                                    } else {
                                        return 'gambar tidak boleh lebih dari 1 MB';
                                    }
                                }
                            } else {
                                return 'cek kembali extension gambar anda';
                            }
                        }
                    }
                }
            }

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('office_user_list'));
        }


        $arrBpjs = [];

        $bpjs = json_decode($data->getBpjs());

        if (isset($bpjs)) {
            foreach ($bpjs as $item) {
                array_push($arrBpjs, $item);
            }
        }


        $arrNoTelp = [];

        $noTelp = json_decode($data->getNoTelp());

        if (isset($noTelp)) {
            foreach ($noTelp as $item) {
                array_push($arrNoTelp, $item);
            }
        }


        $arrPakaian = [];

        $ukPakaian = json_decode($data->getUkuranPakaian());

        if (isset($ukPakaian)) {
            foreach ($ukPakaian as $item) {
                array_push($arrPakaian, $item);
            }
        }

        return $this->render('OfficeBundle:user:update-user.html.twig', [
            'data' => $data,
            'bpjs' => $arrBpjs,
            'telp' => $arrNoTelp,
            'pakaian' => $arrPakaian,
            'penempatan' => $penempatan,
            'job' => $job,
            'shift' => $shift
        ]);
    }

    public function updateUserJobAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $userData = $this->get('security.token_storage')->getToken()->getUser();

        $user = $em->getRepository(UserPersonal::class)->findById($userData->getId());

//        $shift = $em->getRepository(Shift::class)->findAll();

        $data = $em->getRepository(UserJob::class)->findByUserId($user);

        if ($request->getMethod() == 'POST') {
            if ($data instanceof UserJob) {
                $data->setGolongan($request->get('golongan'));
                $data->setJenjangPangkat($request->get('jenjang_pangkat'));
//                $data->setTanggalMasuk($request->get('tanggal_masuk'));
                $data->setPengalamanKerjaTerakhir($request->get('pengalaman_kerja'));
                $data->setIsDeleted(0);
//                $data->setShift($em->getRepository(Shift::class)->find($request->get('shift')));
            }
            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('office_update_user_family'));
        }

        return $this->render('OfficeBundle:user:update-user-job.html.twig', [
            'data' => $data
        ]);
    }

    public function updateUserFamilyAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $userData = $this->get('security.token_storage')->getToken()->getUser();

        $user = $em->getRepository(UserPersonal::class)->findById($userData->getId());

        $data = $em->getRepository(UserFamily::class)->findByUserId($user);

        if ($request->getMethod() == 'POST') {
            if ($data instanceof UserFamily) {
                $data->setPasangan(json_encode($request->get('pasangan')));
                $data->setStatusPerkawinan($request->get('status_perkawinan'));
                $data->setAlamatOrangTua($request->get('alamat_orang_tua'));
                $data->setOrangTua(json_encode($request->get('orang_tua')));
                $data->setMertua(json_encode($request->get('mertua')));
                $data->setAlamatMertua($request->get('alamat_mertua'));
            }

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('office_user_list'));
        }

        $arrPasangan = [];

        $pasangan = json_decode($data->getPasangan());

        if (isset($pasangan)) {
            foreach ($pasangan as $item) {
                array_push($arrPasangan, $item);
            }
        }

        $arrOrangTua = [];

        $orangTua = json_decode($data->getOrangTua());

        if (isset($orangTua)) {
            foreach ($orangTua as $item) {
                array_push($arrOrangTua, $item);
            }
        }

        $arrAyah = [];
        $arrIbu = [];

        if (isset($arrOrangTua[0])) {
            foreach ($arrOrangTua[0] as $item) {
                array_push($arrAyah, $item);
            }
        }

        if (isset($arrOrangTua[1])) {
            foreach ($arrOrangTua[1] as $item) {
                array_push($arrIbu, $item);
            }
        }

        $arrMertua = [];

        $mertua = json_decode($data->getMertua());

        if (isset($mertua)) {
            foreach ($mertua as $item) {
                array_push($arrMertua, $item);
            }
        }

        $merAyah = [];
        $merIbu = [];

        if (isset($arrMertua[0])) {
            foreach ($arrMertua[0] as $item) {
                array_push($merAyah, $item);
            }
        }

        if (isset($arrMertua[1])) {
            foreach ($arrMertua[1] as $item) {
                array_push($merIbu, $item);
            }
        }

        return $this->render('OfficeBundle:user:update-user-family.html.twig', ['data' => $data, 'pasangan' => $arrPasangan, 'ayah' => $arrAyah, 'ibu' => $arrIbu, 'merAyah' => $merAyah, 'merIbu' => $merIbu]);
    }

    public function formCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $userData = $this->get('security.token_storage')->getToken()->getUser();

        $pengajuan = $em->getRepository(Attachment::class)->findAll();

        $user = $em->getRepository(UserPersonal::class)->findById($userData->getId());

        $asem = $em->getRepository(Attachment::class)->findByUserId($user);

        $newAdd = [];

        if ($request->getMethod() == 'POST') {
            $data = new Attachment();
            $data->setUserId($user);
            $data->setTypeForm($request->get('type-form'));
            $data->setAbsen($request->get('absen'));
            $data->setDescription($request->get('description'));
            $data->setTglMulai(date('Y-m-d', strtotime($request->get('tanggal_mulai'))));
            $data->setTglAkhir(date('Y-m-d', strtotime($request->get('tanggal_akhir'))));
            $data->setHashDate($user, $request->get('tanggal_mulai'), $request->get('tanggal_akhir'));
            $data->setIsValidated(0);
            $data->setCreatedAt(new \DateTime());

            if ($user->getIsValidated() == 0) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'hubungin admin terlebih dahulu untuk memvalidasi status'
                );
                return $this->redirect($this->generateUrl('office_user_create_form'));
            }

            array_push($newAdd, $data);

            foreach ($newAdd as $data) {
                $em->persist($data);
            }

            foreach ($newAdd as $key => $item) {
                foreach ($pengajuan as $keyPengajuan => $itemPengajuan) {
                    if ($itemPengajuan->getHashDate() == $item->getHashDate()) {
                        unset($newAdd[$key]);
                    }
                }
            }

            try {
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'berhasil melakukan pengajuan izin'
                );
            } catch (UniqueConstraintViolationException $e) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'sudah melakukan pengajuan izin'
                );
            }

                return $this->redirect($this->generateUrl('office_user_success_izin'));
//                if($this->isGranted('ROLE_USER')) {
//                    return $this->redirect($this->generateUrl('office_user_list_form'));
//                }elseif ($this->isGranted('ROLE_ADMIN')) {
//                    return $this->redirect($this->generateUrl('office_admin_list_form'));
//                }elseif ($this->isGranted('ROLE_VALIDATOR')) {
//                    return $this->redirect($this->generateUrl('office_validator_list_form'));
//                }
        }

        return $this->render('OfficeBundle:attachment:create.html.twig', [
            'nik' => $user->getNik(),
            'username' => $user->getUsername()
        ]);
    }

    public function formListAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $userData = $this->get('security.token_storage')->getToken()->getUser();

        $user = $em->getRepository(UserPersonal::class)->findById($userData->getId());

        $dayOff = $em->getRepository(Cuti::class)->findBy(['userId' => $userData->getId()]);

        $data = $em->getRepository(Attachment::class)->findBy(['userId'=>$userData]);

        return $this->render('OfficeBundle:attachment:list.html.twig', [
            'data' => $data,
            'user' => $userData,
            'dayOff' => $dayOff,
        ]);
    }

    public function formUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $userData = $this->get('security.token_storage')->getToken()->getUser();

        $user = $em->getRepository(UserPersonal::class)->findById($userData->getId());

        $data = $em->getRepository(Attachment::class)->findByUserId($user);

        if ($user->getIsValidated() == 0) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'hubungin admin terlebih dahulu untuk memvalidasi status'
            );

            return $this->redirect($this->generateUrl('office_user_list'));
        }

        if ($request->getMethod() == 'POST') {
            if ($data instanceof Attachment) {
                $data->setTypeForm($request->get('type-form'));
                $data->setAbsen($request->get('absen'));
                $data->setDescription($request->get('description'));
                $data->setTglMulai(date('Y-m-d', strtotime($request->get('tanggal_mulai'))));
                $data->setTglAkhir(date('Y-m-d', strtotime($request->get('tanggal_akhir'))));
                $data->setIsValidated(0);
            }
            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('office_user_list_form'));
        }

        return $this->render('OfficeBundle:attachment:update.html.twig', ['data' => $data, 'nik' => $user->getNik(), 'username' => $user->getUsername()]);
    }

    public function forgotPasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(UserPersonal::class)->findByEmail($request->get('email'));

        if ($request->getMethod() == 'POST') {
            $token = md5(uniqid());
            if ($data instanceof UserPersonal) {
                $data->setToken($token);
            }
            $em->persist($data);
            $em->flush();

            $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls')
                        ->setUsername('manajemen.rihansvariza@gmail.com')
                        ->setPassword('admin@mrv');

            $message = \Swift_Message::newInstance();
            $message->setSubject('Reset Password');
            $message->setFrom('admin@mrv.com');
            $message->setTo([$request->get('email')]);
            $message->setBody($this->renderView('OfficeBundle:user:reset-tmp.html.twig', ['username' => $data->getUsername(), 'host' => $request->getHost(), 'token' => $token]), 'text/html');

            $mailer = \Swift_Mailer::newInstance($transport);
            $mailer->send($message);

            return $this->redirect($this->generateUrl('office_reset_password', ['token' => $token]));
        }

        return $this->render('OfficeBundle:user:forgot-password.html.twig');
    }

    public function resetAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(UserPersonal::class)->findByToken($request->get('token'));

//        return var_dump();

        if ($request->getMethod() == 'POST') {
            if ($data[0] instanceof UserPersonal) {
                $data[0]->setPassword($request->get('password'));
                $data[0]->setToken('');
            }
            $em->persist($data[0]);
            $em->flush();

            return $this->redirect($this->generateUrl('office_login'));
        }

        return $this->render('OfficeBundle:user:password-reset.html.twig');
    }

    public function successAction()
    {
        return $this->render('OfficeBundle:Default:message.html.twig');
    }

    public function individualReportAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $bpjs = json_decode($user->getBpjs());

        $arrBpjs = [];

        if (isset($bpjs)) {
            foreach ($bpjs as $item) {
                array_push($arrBpjs, $item);
            }
        }

        $noTelp = json_decode($user->getNoTelp());

        $arrNoTelp = [];

        if (isset($noTelp)) {
            foreach ($noTelp as $item) {
                array_push($arrNoTelp, $item);
            }
        }

        $job = $em->getRepository(UserJob::class)->findByUserId($user);

        $family = $em->getRepository(UserFamily::class)->findByUserId($user);

        return $this->render('OfficeBundle:user:report-individu.html.twig', [
            'user' => $user,
            'job' => $job,
            'family' => $family,
            'bpjs' => $arrBpjs,
            'telefon' => $arrNoTelp,
        ]);
    }

    public function formAbsenAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $em->getRepository(Attachment::class)->findByUserId($user);

        return $this->render('OfficeBundle:user:form-absen.html.twig', [
            'data' => $data
        ]);
    }

    public function formCutiAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $cutii = $user->getJob()->getQuotas();

        $cuti = $em->getRepository(Cuti::class)->findOneBy(['userId'=>$user]);

        $cutiAll = $em->getRepository(Cuti::class)->findBy(['userId'=>$user]);

        $totalCuti = count($cutiAll);

//        return var_dump($totalCuti);

        $hasil = $cutii - count($cutiAll);

//        return var_dump($totalCuti);

//        $arrCuti = [];
//
//        for ($i=0; $i < count($cuti); ++$i) {
//            $awal = $cuti[$i]->getAbsDate();
//        }

//        return var_dump($awal);

        $userData = $em->getRepository(UserPersonal::class)->find($user);

        return $this->render('OfficeBundle:user:form-cuti.html.twig', [
            'data' => $userData,
            'total' => $totalCuti,
            'cuti' => $cuti,
            'total_cuti' => $cutii,
            'hasil' => $hasil
        ]);
    }

    public function absenAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        // initiate month and date
        $givenMonth = date('m');
        $givenYear = date('Y');
        $givenCompany = null;

        $yearPop = [date('Y')];
        for ($i = 1; $i < 5; ++$i) {
            array_push($yearPop, date('Y', strtotime('-' . $i . ' year')));
        }

        if ($request->get('month') != null) {
            $givenMonth = $request->get('month');
        }

        if ($request->get('year') != null) {
            $givenYear = $request->get('year');
        }

        if ($request->get('company') != null) {
            $givenCompany = $request->get('company');
        }

        $userData = $this->get('security.token_storage')->getToken()->getUser();

        if ($request->get('company') != null) {
            if ($request->get('company') != 0) {
                $userData = $manager->getRepository(UserPersonal::class)->findBy([
                    'penempatan' => $request->get('company'),
                ]);
            }
        }

        $presenceDataRaw = $manager->getRepository(UserPresence::class)->findBy([
            'month' => $givenMonth,
            'year' => $givenYear,
        ]);

        /*
         * Calculating absence and presence of user.
         */

        $dataAbsen = [];

        $allDataPresence = $manager->getRepository(UserPresence::class)->findBy([
            'month' => $givenMonth,
            'year' => $givenYear,
            'userId' => $userData,
        ]);

        $arrDuplicate = [];

        foreach ($allDataPresence as $item) {
            $item->setAbsoluteDay(base64_encode($item->getUserId()->getId() .' '. $item->getCreatedAt()->format('d m Y')));
            array_push($arrDuplicate, $item->getAbsoluteDay());
        }


        $unique = array_unique($arrDuplicate, SORT_REGULAR);

        $diffCellUniq = array_diff_key($arrDuplicate, $unique);

        foreach ($diffCellUniq as $uniqueKey => $uniqueValue) {
            unset($allDataPresence[$uniqueKey]);
        }

        foreach ($allDataPresence as $item) {
            $userData->setPresenceRaw($userData->getPresenceRaw() + 1);
        }

        $company = $manager->getRepository(CompanyProfile::class)->findAll();

        $dayCount = cal_days_in_month(CAL_GREGORIAN, $givenMonth, $givenYear);

        $monthHoliday = 0;

        for ($i = 1; $i <= $dayCount; $i++) {
            $dayName = new \DateTime($i . '-' . $givenMonth . '-' . $givenYear);

            if ($dayName->format('D') == 'Sun') {
                $monthHoliday++;
            }
        }

        $holiday = $manager->getRepository(Holiday::class)->findBy(['month' => $givenMonth, 'year' => $givenYear]);

        $monthHoliday = $monthHoliday + count($holiday);

        return $this->render('OfficeBundle:user:absensi.html.twig', [
            'data' => $userData,
            'yearPop' => $yearPop,
            'presence' => $presenceDataRaw,
            'month' => $givenMonth,
            'year' => $givenYear,
            'dayCount' => $dayCount - $monthHoliday,
        ]);
    }

    public function absenDetailAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $managerConfig = $manager->getConfiguration();
        $dateNow = new \DateTime();
        $givenMonth = $request->get('month');
        $givenYear = $request->get('year');
        $requestedDate = new \DateTime('1-'.$givenMonth.'-'.$givenYear);
        $presenceRepository = $manager->getRepository(UserPresence::class);
        $dayOfMonth = cal_days_in_month(CAL_GREGORIAN, $requestedDate->format('m'), $requestedDate->format('Y'));

        $user = $manager->getRepository(UserPersonal::class)->find($request->get('id'));

        $dataMasuk = [];
        $dataPulang = [];

        $dataMasukRaw = $presenceRepository->findBy([
            'state' => -1,
            'userId' => $request->get('id'),
            'month' => $givenMonth,
            'year' => $givenYear,
        ]);

        $dataPulangRaw = $presenceRepository->findBy([
            'state' => 1,
            'userId' => $request->get('id'),
            'month' => $givenMonth,
            'year' => $givenYear,
        ]);

        foreach ($dataMasukRaw as $item) {
            $dataMasuk[$item->getDay()]['state'] = -1;
            $dataMasuk[$item->getDay()]['date'] = $item->getCreatedAt();
        }

        foreach ($dataPulangRaw as $item) {
            $dataPulang[$item->getDay()]['state'] = 1;
            $dataPulang[$item->getDay()]['date'] = $item->getCreatedAt();
        }

        $holidayRaw = $manager->getRepository(Holiday::class)->findBy([
            'month' => $givenMonth,
            'year' => $givenYear,
        ]);
        $holiday = [];

        foreach ($holidayRaw as $item) {
            $holiday[$item->getDays()]['state'] = 1;
            $holiday[$item->getDays()]['title'] = $item->getTitle();
        }

        $variable['month'] = \DateTime::createFromFormat('m', $givenMonth)->format('M');
        $variable['year'] = \DateTime::createFromFormat('Y', $givenYear)->format('Y');

        $managerConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');

        $variable['cuti'] = $manager->createQueryBuilder()
            ->select('c')
            ->from('OfficeBundle:Cuti', 'c')
            ->where('c.userId = :userId')
            ->andWhere('c.isValidated = 1')
            ->andWhere('MONTH(c.absDate) = :desireMonth')
            ->setParameter('userId', $request->get('id'))
            ->setParameter('desireMonth', $givenMonth)
            ->getQuery()->getResult();

        $variable['attachment'] = $manager->createQueryBuilder()
            ->select('a')
            ->from('OfficeBundle:Attachment', 'a')
            ->where('a.userId = :userId')
            ->andWhere('MONTH(a.tglMulai) = :desireMonth')
            ->setParameter('userId', $request->get('id'))
            ->setParameter('desireMonth', $givenMonth)
            ->getQuery()->getResult();

        /**
         * Getting Detail of attachment.
         */
        $newDatePopulate = [];

        foreach ($variable['attachment'] as $itemRaw) {
            $startDate = $itemRaw->getTglMulai();
            $endDate = $itemRaw->getTglAkhir();

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($startDate, $interval, $endDate);

            foreach ($period as $dt) {
                $newDatePopulate[$dt->format('d-m-Y')] = [$itemRaw->getAbsen() ,$itemRaw->getDescription()];
            }

            $newDatePopulate[$endDate->format('d-m-Y')] = [$itemRaw->getAbsen() ,$itemRaw->getDescription()];
        }

        /**
         * Checking if populated date out of month.
         */
        foreach ($newDatePopulate as $key => $value) {
            $checkDate = new \DateTime($key);

            if ($checkDate->format('m') != $givenMonth) {
                unset($newDatePopulate[$key]);
            }
        }

        return $this->render('OfficeBundle:user:absen-detail.html.twig', [
            'monthCount' => $dayOfMonth,
            'dataMasuk' => $dataMasuk,
            'dataPulang' => $dataPulang,
            'holiday' => $holiday,
            'user' => $user,
            'variable' => $variable,
            'newDatePopulate' => $newDatePopulate,
        ]);
    }
}
