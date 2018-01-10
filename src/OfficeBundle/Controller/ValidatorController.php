<?php

/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 02/07/17
 * Time: 20:20.
 */

namespace OfficeBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OfficeBundle\Entity\Attachment;
use OfficeBundle\Entity\CompanyProfile;
use OfficeBundle\Entity\Cuti;
use OfficeBundle\Entity\DayType;
use OfficeBundle\Entity\Holiday;
use OfficeBundle\Entity\ImageResize;
use OfficeBundle\Entity\Shift;
use OfficeBundle\Entity\UserFamily;
use OfficeBundle\Entity\UserJob;
use OfficeBundle\Entity\UserPersonal;
use OfficeBundle\Entity\UserPresence;
use OfficeBundle\Services\UserPersonalServices;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ValidatorController extends Controller
{
    public function indexAction()
    {
        return $this->render('OfficeBundle:home:index.html.twig');
    }

    public function updateDataUserAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $penempatan = $em->getRepository(CompanyProfile::class)->findAll();

        $data = $em->getRepository(UserPersonal::class)->findById($id);

        $job = $em->getRepository(UserJob::class)->findByUserId($data->getId());

        $shift = $em->getRepository(Shift::class)->findAll();

        if ('POST' == $request->getMethod()) {
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
                    $data->setNik($request->get('nik'));
                    $data->setNoRegistrasi($request->get('no_registrasi'));
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
                        @mkdir($this->getParameter('profile_directory')['resource'], 0755, true);
                    }

                    if (!empty($request->files->get('profile_picture'))) {
                        $file = $request->files->get('profile_picture');

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
                                    return 'ukuran tidak boleh lebih dari 1MB';
                                }
                            }
                        } else {
                            return 'cek extension';
                        }
                    }
                }

                $em->persist($data);

                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Update Data User Has been Success'
                );

                return $this->redirect($this->generateUrl('office_validator_list'));
            }
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

        return $this->render('OfficeBundle:validator:update-user.html.twig', [
            'data' => $data,
            'bpjs' => $arrBpjs,
            'telp' => $arrNoTelp,
            'pakaian' => $arrPakaian,
            'penempatan' => $penempatan,
            'job' => $job,
            'shift' => $shift,
        ]);
    }

    public function updateDataJobAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->findById($id);

        $data = $em->getRepository(UserJob::class)->findByUserId($user);

        if ('POST' == $request->getMethod()) {
            if ($data instanceof UserJob) {
                $data->setGolongan($request->get('golongan'));
                $data->setJenjangPangkat($request->get('jenjang_pangkat'));
//                $data->setTanggalMasuk($request->get('tanggal_masuk'));
                $data->setPengalamanKerjaTerakhir($request->get('pengalaman_kerja'));
                $data->setKontrakTraining(json_encode($request->get('kontrak_training')));
                $data->setKontrakKerja(json_encode($request->get('kontrak_kerja')));
                $data->setTanggalPercobaan($request->get('tanggal_percobaan'));
                $data->setTanggalSkTetap($request->get('tanggal_tetap'));
                $data->setIsDeleted(0);
            }

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('office_validator_update_family', ['id' => $user->getId()]));
        }

        $arrTraining = [];

        $training = json_decode($data->getKontrakTraining());

        if (isset($training)) {
            foreach ($training as $item) {
                array_push($arrTraining, $item);
            }
        }

        $arrKerja = [];

        $kerja = json_decode($data->getKontrakKerja());

        if (isset($kerja)) {
            foreach ($kerja as $item) {
                array_push($arrKerja, $item);
            }
        }

        $arrStart = [];
        $arrEnd = [];

        if (isset($arrKerja[0])) {
            foreach ($arrKerja[0] as $item) {
                array_push($arrStart, $item);
            }
        }

        if (isset($arrKerja[1])) {
            foreach ($arrKerja[1] as $item) {
                array_push($arrEnd, $item);
            }
        }

        return $this->render('OfficeBundle:validator:update-job.html.twig', [
            'data' => $data,
            'training' => $arrTraining,
            'start' => $arrStart,
            'end' => $arrEnd,
        ]);
    }

    public function updateDataFamilyAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->findById($id);

//        return var_dump($user);

        $data = $em->getRepository(UserFamily::class)->findByUserId($user);

//        return var_dump($user);

        if ('POST' == $request->getMethod()) {
            if ($data instanceof UserFamily) {
                $data->setUserId($user);
                $data->setPasangan(json_encode($request->get('pasangan')));
                $data->setStatusPerkawinan($request->get('status_perkawinan'));
                $data->setAlamatOrangTua($request->get('alamat_orang_tua'));
                $data->setOrangTua(json_encode($request->get('orang_tua')));
                $data->setMertua(json_encode($request->get('mertua')));
                $data->setAlamatMertua($request->get('alamat_mertua'));
            }

//            return var_dump($data);
            $em->persist($data);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Update data job and family has been updated'
            );

            return $this->redirect($this->generateUrl('office_validator_list'));
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

        return $this->render('OfficeBundle:validator:update-family.html.twig', [
            'data' => $data,
            'pasangan' => $arrPasangan,
            'ayah' => $arrAyah,
            'ibu' => $arrIbu,
            'merAyah' => $merAyah,
            'merIbu' => $merIbu,
        ]);
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(UserPersonal::class)->findAll();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('OfficeBundle:validator:list-user.html.twig', [
            'data' => $data,
            'user' => $user,
        ]);
    }

    public function editStatusAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(UserPersonal::class)->findById($id);

        if ('POST' == $request->getMethod()) {
            if ($data instanceof UserPersonal) {
                $data->setStatus($request->get('status'));
            }
            $em->persist($data);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Editing status has been success'
            );

            return $this->redirect($this->generateUrl('office_validator_list'));
        }

        return $this->render('OfficeBundle:validator:edit-status.html.twig', ['data' => $data]);
    }

    public function editValidateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(UserPersonal::class)->findById($id);

        if ('POST' == $request->getMethod()) {
            UserPersonalServices::changeValidate($data);

            $em->persist($data);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'data validated has been changes'
            );

            return $this->redirect($this->generateUrl('office_validator_list'));
        }

        return $this->render('OfficeBundle:validator:edit-validate.html.twig', array('data' => $data));
    }

    public function formListAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Attachment::class)->findAll();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('OfficeBundle:validator:list-form.html.twig', [
            'user' => $user,
            'data' => $data,
        ]);
    }

    public function formCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $newAdd = [];

        $pengajuan = $em->getRepository(Attachment::class)->findAll();

        $data = $em->getRepository(Attachment::class)->find($user->getId());

        if ('POST' == $request->getMethod()) {
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

            return $this->redirect($this->generateUrl('office_validator_list_form'));
        }

        return $this->render('OfficeBundle:validator:create-form.html.twig', ['nik' => $user->getNik(), 'username' => $user->getUsername()]);
    }

    public function formCutiAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Cuti::class)->findAll();
    }

    public function dayOffInputAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $userdata = $this->get('security.token_storage')->getToken()->getUser();

        $quotas = 6;
        $type = $manager->getRepository(DayType::class)->findAll();

        return $this->render('OfficeBundle:validator:dayoff-input.html.twig', [
            'user' => $userdata,
            'quotas' => $quotas,
            'type' => $type,
        ]);
    }

    public function updateFormAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->find($id);

        $data = $em->getRepository(Attachment::class)->findByUserId($user);

        if ('POST' == $request->getMethod()) {
            if ($data instanceof Attachment) {
                $data->setTypeForm($request->get('type-form'));
                $data->setDescription($request->get('description'));
                $data->setAbsen($request->get('absen'));
                $data->setTglMulai($request->get('tgl_mulai'));
                $data->setTglAkhir($request->get('tgl_akhir'));
                $data->setIsValidated(0);
            }

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('office_validator_list_form'));
        }

        return $this->render('OfficeBundle:validator:update-form.html.twig', [
            'data' => $data,
            'username' => $user->getUsername(),
            'nik' => $user->getNik(),
        ]);
    }

    public function listPengajuanAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $dayOff = $em->createQueryBuilder()
            ->select('user')
            ->from('OfficeBundle:UserPersonal', 'user')
            ->innerJoin('OfficeBundle:Cuti', 'cuti')
            ->where('user.id = cuti.userId');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('OfficeBundle:validator:list-pengajuan.html.twig', [
            'dayOff' => $dayOff->getQuery()->getResult(),
            'user' => $user,
        ]);
    }

    public function deleteFormAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Attachment::class)->find($id);

        $em->remove($data);

        $em->flush();

        return $this->redirect($this->generateUrl('office_validator_list_form'));
    }

    public function detailDayAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $data['cuti'] = $manager->getRepository(Cuti::class)->findBy([
            'userId' => $request->get('user_id'),
        ]);

        $data['user'] = $manager->getRepository(UserPersonal::class)->find($request->get('user_id'));

        return $this->render('OfficeBundle:validator:detail-dayoff.html.twig', [
            'data' => $data,
        ]);
    }

    public function createShiftAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if ('POST' == $request->getMethod()) {
            $data = new Shift();
            $data->setLabel($request->get('keterangan'));
            $data->setStartTime($request->get('jam-masuk'));
            $data->setEndTime($request->get('jam-pulang'));
            $data->setCreatedAt(new \DateTime());

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('office_validator_list_shift'));
        }

        return $this->render('OfficeBundle:validator:create-shift.html.twig');
    }

    public function listShiftAction()
    {
        $data = $this->getDoctrine()->getEntityManager()->getRepository(Shift::class)->findAll();

        return $this->render('OfficeBundle:validator:list-shift.html.twig', [
            'data' => $data,
        ]);
    }

    public function validateFormAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Attachment::class)->find($id);

        return $this->render('OfficeBundle:validator:validator-form.html.twig', [
            'data' => $data,
        ]);
    }

    public function approveFormAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Attachment::class)->find($id);

        $data->setIsValidated(1);
        $em->persist($data);

        $em->flush();

        return $this->redirect($this->generateUrl('office_validator_list_form'));
    }

    public function rejectFormAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Attachment::class)->find($id);

        $data->setIsValidated(2);
        $em->persist($data);
        $em->flush();

        return $this->redirect($this->generateUrl('office_validator_list_form'));
    }

    public function absenValidatorAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        // initiate month and date
        $givenMonth = date('m');
        $givenYear = date('Y');
        $givenCompany = null;

        $yearPop = [date('Y')];
        for ($i = 1; $i < 5; ++$i) {
            array_push($yearPop, date('Y', strtotime('-'.$i.' year')));
        }

        if (null != $request->get('month')) {
            $givenMonth = $request->get('month');
        }

        if (null != $request->get('year')) {
            $givenYear = $request->get('year');
        }

        if (null != $request->get('company')) {
            $givenCompany = $request->get('company');
        }

        $userData = $manager->getRepository(UserPersonal::class)->findAll();

        $userLogin = $this->get('security.token_storage')->getToken()->getUser();

        if (null != $request->get('company')) {
            if (0 != $request->get('company')) {
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
        ]);

        $arrDuplicate = [];

        foreach ($allDataPresence as $item) {
            $item->setAbsoluteDay(base64_encode($item->getUserId()->getId().' '.$item->getCreatedAt()->format('d m Y')));
            array_push($arrDuplicate, $item->getAbsoluteDay());
        }

        $unique = array_unique($arrDuplicate, SORT_REGULAR);

        $diffCellUniq = array_diff_key($arrDuplicate, $unique);

        foreach ($diffCellUniq as $uniqueKey => $uniqueValue) {
            unset($allDataPresence[$uniqueKey]);
        }

        foreach ($allDataPresence as $item) {
            foreach ($userData as $user) {
                if ($item->getUserId() == $user) {
                    $user->setPresenceRaw($user->getPresenceRaw() + 1);
                }
            }
        }

        $company = $manager->getRepository(CompanyProfile::class)->findAll();

        $dayCount = cal_days_in_month(CAL_GREGORIAN, $givenMonth, $givenYear);

        $monthHoliday = 0;

        for ($i = 1; $i <= $dayCount; ++$i) {
            $dayName = new \DateTime($i.'-'.$givenMonth.'-'.$givenYear);

            if ('Sun' == $dayName->format('D')) {
                ++$monthHoliday;
            }
        }

        $holiday = $manager->getRepository(Holiday::class)->findBy(['month' => $givenMonth, 'year' => $givenYear]);

        $monthHoliday = $monthHoliday + count($holiday);

        return $this->render('OfficeBundle:validator:absensi-validator.html.twig', [
            'data' => $userData,
            'yearPop' => $yearPop,
            'presence' => $presenceDataRaw,
            'month' => $givenMonth,
            'year' => $givenYear,
            'dayCount' => $dayCount - $monthHoliday,
            'userLogin' => $userLogin,
        ]);
    }

    public function absenValidatorDetailAction(Request $request)
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
                $newDatePopulate[$dt->format('d-m-Y')] = [$itemRaw->getAbsen(), $itemRaw->getDescription()];
            }

            $newDatePopulate[$endDate->format('d-m-Y')] = [$itemRaw->getAbsen(), $itemRaw->getDescription()];
        }

        /*
         * Checking if populated date out of month.
         */
        foreach ($newDatePopulate as $key => $value) {
            $checkDate = new \DateTime($key);

            if ($checkDate->format('m') != $givenMonth) {
                unset($newDatePopulate[$key]);
            }
        }

        return $this->render('OfficeBundle:validator:absensi-validator-detail.html.twig', [
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
