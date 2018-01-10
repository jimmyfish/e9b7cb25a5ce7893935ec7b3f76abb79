<?php

/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 11/06/17
 * Time: 21:58.
 */

namespace OfficeBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OfficeBundle\Entity\Attachment;
use OfficeBundle\Entity\CompanyProfile;
use OfficeBundle\Entity\DayType;
use OfficeBundle\Entity\ImageResize;
use OfficeBundle\Entity\Shift;
use OfficeBundle\Entity\UserFamily;
use OfficeBundle\Entity\UserJob;
use OfficeBundle\Entity\UserPersonal;
use OfficeBundle\Entity\Cuti;
use OfficeBundle\Services\UserPersonalServices;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function indexAction()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
        }

        return $this->render('OfficeBundle:home:index.html.twig');
    }

    public function deleteUserAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(UserPersonal::class)->findById($id);

        $em->remove($data);

        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'data has been deleted'
        );

//        if (!file_exists($this->getParameter('profile_directory')['resource'].'/'.$data->getProfilePicture())) {
//            unlink($this->getParameter('profile_directory')['resource'].'/'.$data->getProfilePicture());
//        }

        return $this->redirect($this->generateUrl('office_admin_list'));
    }

    public function updateUserAction($id, Request $request)
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

                        if (null != $file) {
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
                }

                $em->persist($data);

                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Update Data User Has been Success'
                );

                return $this->redirect($this->generateUrl('office_admin_list'));
            }
        }

        $bpjs = json_decode($data->getBpjs());

        $arrBpjs = [];

        if (isset($bpjs)) {
            foreach ($bpjs as $item) {
                array_push($arrBpjs, $item);
            }
        }

        $noTelp = json_decode($data->getNoTelp());

        $arrNoTelp = [];

        if (isset($noTelp)) {
            foreach ($noTelp as $item) {
                array_push($arrNoTelp, $item);
            }
        }

        $ukPakaian = json_decode($data->getUkuranPakaian());

        $arrPakaian = [];

        if (isset($ukPakaian)) {
            foreach ($ukPakaian as $item) {
                array_push($arrPakaian, $item);
            }
        }

        return $this->render('OfficeBundle:admin:update-user.html.twig', [
            'data' => $data,
            'bpjs' => $arrBpjs,
            'telp' => $arrNoTelp,
            'pakaian' => $arrPakaian,
            'penempatan' => $penempatan,
            'job' => $job,
            'shift' => $shift,
        ]);
    }

    public function updateDataJobAction($id, Request $request)
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

            return $this->redirect($this->generateUrl('office_admin_update_family', ['id' => $user->getId()]));
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

        return $this->render('OfficeBundle:admin:update-user-job.html.twig', [
            'data' => $data,
            'training' => $arrTraining,
            'start' => $arrStart,
            'end' => $arrEnd,
        ]);
    }

    public function validateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->findById($id);

        if ('POST' == $request->getMethod()) {
            $user->setIsvalidated(1);

            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('office_admin_list'));
        }

        return 'OK';
    }

    public function viewDocAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->findById($id);

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

        $ukPakaian = json_decode($user->getUkuranPakaian());

        $arrPakaian = [];

        if (isset($ukPakaian)) {
            foreach ($ukPakaian as $item) {
                array_push($arrPakaian, $item);
            }
        }

        $job = $em->getRepository(UserJob::class)->findByUserId($user);

        $family = $em->getRepository(UserFamily::class)->findByUserId($user);

        return $this->render('OfficeBundle:admin:view-validate.html.twig', ['user' => $user, 'job' => $job, 'family' => $family, 'bpjs' => $arrBpjs, 'pakaian' => $arrPakaian, 'no_telp' => $arrNoTelp]);
    }

    public function updateDataFamilyAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->findById($id);

        $data = $em->getRepository(UserFamily::class)->findByUserId($user);

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

            $em->persist($data);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Update data job and family has been updated'
            );

            return $this->redirect($this->generateUrl('office_admin_list'));
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

        return $this->render('OfficeBundle:admin:update-user-family.html.twig', [
            'data' => $data,
            'pasangan' => $arrPasangan,
            'ayah' => $arrAyah,
            'ibu' => $arrIbu,
            'merAyah' => $merAyah,
            'merIbu' => $merIbu,
        ]);
    }

    public function statusUjiCobaAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('u')->from(UserPersonal::class, 'u')->where('u.status = 0')->orderBy('u.nama', 'DESC');

        $testing = $qb->getQuery()->getResult();

        return $this->render('OfficeBundle:admin:list-karyawan-uji-coba.html.twig', array('test' => $testing));
    }

    public function statusKontrakAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(UserPersonal::class, 'u')->where('u.status = 1')->orderBy('u.nama', 'DESC');

        $kampret = $qb->getQuery()->getResult();

        return $this->render('OfficeBundle:admin:list-karyawan-kontrak.html.twig', array('test' => $kampret));
    }

    public function statusPkwtAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(UserPersonal::class, 'u')->where('u.status = 2')->orderBy('u.nama', 'DESC');

        $data = $qb->getQuery()->getResult();

        return $this->render('OfficeBundle:admin:list-pkwt.html.twig', ['data' => $data]);
    }

    public function statusPkwttAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(UserPersonal::class, 'u')->where('u.status = 3')->orderBy('u.nama', 'DESC');

        $data = $qb->getQuery()->getResult();

        return $this->render('OfficeBundle:admin:list-pkwtt.html.twig', ['data' => $data]);
    }

    public function editStatusAction(Request $request)
    {
        $id = $request->get('id');

        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(UserPersonal::class)->findById($id);

        if ($data instanceof UserPersonal) {
            if ('POST' == $request->getMethod()) {
                $data->setStatus($request->get('status'));

                $em->persist($data);
                $em->flush();

                if ($data instanceof UserPersonal) {
                    if (0 == $data->getStatus()) {
                        return $this->redirect($this->generateUrl('office_admin_uji_coba'));
                    } elseif (1 == $data->getStatus()) {
                        return $this->redirect($this->generateUrl('office_admin_kontrak'));
                    } elseif (2 == $data->getStatus()) {
                        return $this->redirect($this->generateUrl('office_admin_pkwt'));
                    } elseif (3 == $data->getStatus()) {
                        return $this->redirect($this->generateUrl('office_admin_pkwtt'));
                    } else {
                        return $this->redirect($this->generateUrl('office_admin_tetap'));
                    }
                }
            }
        }

        return $this->render('OfficeBundle:admin:edit-status.html.twig', array('data' => $data));
    }

    public function editValidateAction(Request $request, $id)
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

            return $this->redirect($this->generateUrl('office_admin_list'));
        }

        return $this->render('OfficeBundle:admin:edit-validate.html.twig', array('data' => $data));
    }

    public function editActiveAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(UserPersonal::class)->findById($id);

        if ('POST' == $request->getMethod()) {
            UserPersonalServices::changeActive($data);

            $em->persist($data);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'edit validate has been change'
            );

            return $this->redirect($this->generateUrl('office_admin_list'));
        }

        return $this->render('OfficeBundle:admin:edit-active.html.twig', ['data' => $data]);
    }

    public function statusTetapAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(UserPersonal::class, 'u')->where('u.status = 4')->orderBy('u.nama', 'DESC');

        $asem = $qb->getQuery()->getResult();

        return $this->render('OfficeBundle:admin:list-karyawan-tetap.html.twig', array('test' => $asem));
    }

    public function notificationAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(Attachment::class, 'u')->where('u.isValidated = 0');

        $asem = $qb->getQuery()->getResult();

//        return $this->render('subLayout.html.twig',['asem'=>$asem]);
    }

    public function showUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

//        $penempatan = $em->getRepository(CompanyProfile::class)->findAll();

        $data = $em->getRepository(UserPersonal::class)->findAll();

        return $this->render('OfficeBundle:admin:list.html.twig', ['data' => $data]);
    }

    public function listPenempatanAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(CompanyProfile::class)->findAll();

        return $this->render('OfficeBundle:admin:list-penempatan.html.twig', ['data' => $data]);
    }

    public function deletePenempatanAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(CompanyProfile::class)->find($id);

        $em->remove($data);

        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Data Penempatan Berhasil Dihapus'
        );

        return $this->redirect($this->generateUrl('office_admin_list_penempatan'));
    }

    public function updatePenempatanAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(CompanyProfile::class)->find($id);

        if ('POST' == $request->getMethod()) {
            if ($data instanceof CompanyProfile) {
                $data->setNamaPerusahaan($request->get('nama-perusahaan'));
                $data->setIsDeleted(0);
            }
            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('office_admin_list_penempatan'));
        }

        return $this->render('OfficeBundle:admin:update-penempatan.html.twig', ['data' => $data]);
    }

    public function formCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $userData = $this->get('security.token_storage')->getToken()->getUser();

        $pengajuan = $em->getRepository(Attachment::class)->findAll();

        $user = $em->getRepository(UserPersonal::class)->findById($userData->getId());

        $data = $em->getRepository(Attachment::class)->findByUserId($user);

        $newAdd = [];

        if ('POST' == $request->getMethod()) {
            $data = new Attachment();
            $data->setUserId($user);
            $data->setTypeForm($request->get('type-form'));
            $data->setAbsen($request->get('absen'));
            $data->setDescription($request->get('description'));
            $data->setTglMulai(date('Y-m-d', strtotime($request->get('tanggal_mulai'))));
            $data->setTglAkhir(date('Y-m-d', strtotime($request->get('tanggal_akhir'))));
            $data->setHashDate($userData, $request->get('tanggal_mulai'), $request->get('tanggal_akhir'));
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

//            $em->persist($data);

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

            return $this->redirect($this->generateUrl('office_admin_list_form'));
        }

        return $this->render('OfficeBundle:admin:create-form.html.twig', [
            'data' => $data,
            'nik' => $user->getNik(),
            'username' => $user->getUsername(),
        ]);
    }

    public function dayOffInputAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $userdata = $this->get('security.token_storage')->getToken()->getUser();

        $quotas = 6;
        $type = $manager->getRepository(DayType::class)->findAll();

        return $this->render('OfficeBundle:admin:day-off.html.twig', [
            'user' => $userdata,
            'quotas' => $quotas,
            'type' => $type,
        ]);
    }

    public function formListAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Attachment::class)->findAll();

        $dayOff = $em->createQueryBuilder()
            ->select('user')
            ->from('OfficeBundle:UserPersonal', 'user')
            ->innerJoin('OfficeBundle:Cuti', 'cuti')
            ->where('user.id = cuti.userId');

        return $this->render('OfficeBundle:admin:list-form.html.twig', [
            'data' => $data,
            'dayOff' => $dayOff->getQuery()->getResult(),
        ]);
    }

    public function deleteFormAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Attachment::class)->findById($id);

        $em->remove($data);

        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'data form has been deleted'
        );

        return $this->redirect($this->generateUrl('office_admin_list_form'));
    }

    public function updateFormAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->findById($id);

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

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Update Form has been success'
            );

            return $this->redirect($this->generateUrl('office_admin_list_form'));
        }

        return $this->render('OfficeBundle:admin:update-form.html.twig', ['data' => $data, 'nik' => $user->getNik(), 'username' => $user->getUsername()]);
    }

    public function viewValidateFormAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Attachment::class)->findById($id);

        return $this->render('OfficeBundle:admin:validate-form.html.twig', ['data' => $data]);
    }

    public function validateFormAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Attachment::class)->findById($id);

        $data->setIsValidated(1);
        $em->persist($data);
        $em->flush();

        return $this->redirect($this->generateUrl('office_admin_list_form'));
    }

    public function rejectFormAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Attachment::class)->findById($id);

        $data->setIsValidated(2);

        $em->persist($data);
        $em->flush();

        return $this->redirect($this->generateUrl('office_admin_list_form'));
    }

    public function individualAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->findById($id);

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

        $arrTraining = [];

        $training = json_decode($job->getKontrakTraining());
        if (isset($training)) {
            foreach ($training as $item) {
                array_push($arrTraining, $item);
            }
        }

        $arrKerja = [];

        $kerja = json_decode($job->getKontrakKerja());
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

        $family = $em->getRepository(UserFamily::class)->findByUserId($user);

        $arrPasangan = [];

        $pasangan = json_decode($family->getPasangan());

        if (isset($pasangan)) {
            foreach ($pasangan as $item) {
                array_push($arrPasangan, $item);
            }
        }

        $arrOrangTua = [];

        $orangTua = json_decode($family->getOrangTua());

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

        $mertua = json_decode($family->getMertua());

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

        return $this->render('OfficeBundle:laporan:individu.html.twig', [
            'user' => $user,
            'job' => $job,
            'family' => $family,
            'bpjs' => $arrBpjs,
            'telefon' => $arrNoTelp,
            'start' => $arrStart,
            'end' => $arrEnd,
            'pasangan' => $arrPasangan,
            'ayah' => $arrAyah,
            'ibu' => $arrIbu,
            'merAyah' => $merAyah,
            'merIbu' => $merIbu,
            'training' => $arrTraining,
        ]);
    }

    public function beforeAllReportAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $penempatan = $em->getRepository(CompanyProfile::class)->findAll();

        if ('POST' == $request->getMethod()) {
            $session = $request->getSession();

            $session->set('test', ['value' => $request->get('penempatan')]);

            return $this->redirect($this->generateUrl('office_admin_all_report'));
        }

        return $this->render('OfficeBundle:admin:before-report.html.twig', [
            'penempatan' => $penempatan,
        ]);
    }

    public function allUserReportAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $session = $request->getSession();

        $user = $em->getRepository(UserPersonal::class)->findByPenempatan($session->get('test')['value']);

        $arrBpjs = [];

        $arrNoTelp = [];

        for ($i = 0; $i < count($user); ++$i) {
            $bpjs = json_decode($user[$i]->getBpjs());
            if (isset($bpjs)) {
                foreach ($bpjs as $item) {
                    array_push($arrBpjs, $item);
                }
            }

            $noTelp = json_decode($user[$i]->getNoTelp());

            if (isset($noTelp)) {
                foreach ($noTelp as $item) {
                    array_push($arrNoTelp, $item);
                }
            }
        }

        $job = $em->getRepository(UserJob::class)->findBy(['userId' => $user]);

        $arrTraining = [];

        $arrKerja = [];

        $arrStart = [];
        $arrEnd = [];

        for ($i = 0; $i < count($job); ++$i) {
            $training = json_decode($job[$i]->getKontrakTraining());
            if (isset($training)) {
                foreach ($training as $item) {
                    array_push($arrTraining, $item);
                }
            }

            $kerja = json_decode($job[$i]->getKontrakKerja());
            if (isset($kerja)) {
                foreach ($kerja as $item) {
                    array_push($arrKerja, $item);
                }
            }

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
        }

        $family = $em->getRepository(UserFamily::class)->findBy(['userId' => $user]);

        $arrPasangan = [];

        $arrOrangTua = [];

        $arrAyah = [];
        $arrIbu = [];

        $arrMertua = [];

        $merAyah = [];
        $merIbu = [];

        for ($i = 0; $i < count($family); ++$i) {
            $pasangan = json_decode($family[$i]->getPasangan());

            if (isset($pasangan)) {
                foreach ($pasangan as $item) {
                    array_push($arrPasangan, $item);
                }
            }

            $orangTua = json_decode($family[$i]->getOrangTua());

            if (isset($orangTua)) {
                foreach ($orangTua as $item) {
                    array_push($arrOrangTua, $item);
                }
            }

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

            $mertua = json_decode($family[$i]->getMertua());

            if (isset($mertua)) {
                foreach ($mertua as $item) {
                    array_push($arrMertua, $item);
                }
            }

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
        }

        return $this->render('OfficeBundle:admin:all-user-report.html.twig', [
            'user' => $user,
            'job' => $job,
            'family' => $family,
            'bpjs' => $arrBpjs,
            'telefon' => $arrNoTelp,
            'start' => $arrStart,
            'end' => $arrEnd,
            'pasangan' => $arrPasangan,
            'ayah' => $arrAyah,
            'ibu' => $arrIbu,
            'merAyah' => $merAyah,
            'merIbu' => $merIbu,
            'training' => $arrTraining,
        ]);
    }

    public function hakAksesAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $penempatan = $em->getRepository(CompanyProfile::class)->findAll();

        $data = $em->getRepository(UserPersonal::class)->findAll();

        return $this->render('OfficeBundle:admin:hak-akses.html.twig', ['data' => $data]);
    }

    public function listReportAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(UserPersonal::class)->findAll();

        return $this->render('OfficeBundle:admin:list-report.html.twig', ['data' => $data]);
    }

    public function changeAdminAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->findById($id);

        if ($user instanceof UserPersonal) {
            $user->setRoles(serialize(['ROLE_ADMIN']));
        }
        $em->persist($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Change Admin Has been success'
        );

        return $this->redirect($this->generateUrl('office_admin_hak_akses'));
    }

    public function changeValidatorAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->findById($id);

        if ($user instanceof UserPersonal) {
            $user->setRoles(serialize(['ROLE_VALIDATOR']));
        }
        $em->persist($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Change validator has been success'
        );

        return $this->redirect($this->generateUrl('office_admin_hak_akses'));
    }

    public function changeUserAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(UserPersonal::class)->findById($id);

        if ($data instanceof UserPersonal) {
            $data->setRoles(serialize(['ROLE_USER']));
        }

        $em->persist($data);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'notice',
            'Change user has been success'
        );

        return $this->redirect($this->generateUrl('office_admin_hak_akses'));
    }

    public function formReportAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Attachment::class)->findAll();

        return $this->render('OfficeBundle:admin:form-report.html.twig', ['data' => $data]);
    }

    public function integrityCheckAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $data = $manager->getRepository(UserPersonal::class)->findAll();

        return $this->render('OfficeBundle:admin:integrity.html.twig', ['data' => $data]);
    }
}
