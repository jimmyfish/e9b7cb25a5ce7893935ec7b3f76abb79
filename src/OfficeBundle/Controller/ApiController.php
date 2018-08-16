<?php

namespace OfficeBundle\Controller;

use OfficeBundle\Entity\Attachment;
use OfficeBundle\Entity\CompanyProfile;
use OfficeBundle\Entity\Cuti;
use OfficeBundle\Entity\DayType;
use OfficeBundle\Entity\Device;
use OfficeBundle\Entity\Fingerprint;
use OfficeBundle\Entity\Holiday;
use OfficeBundle\Entity\Shift;
use OfficeBundle\Entity\UserFamily;
use OfficeBundle\Entity\UserJob;
use OfficeBundle\Entity\UserPersonal;
use OfficeBundle\Entity\UserPresence;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    public function listUserFromUsernameAction($username)
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(UserPersonal::class)->findBy(['username' => $username]);

        return new JsonResponse($data);
    }

    public function listHolidayAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $data = $manager->getRepository(Holiday::class)->findAll();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['id'] = $item->getId();
            $results[$i]['title'] = $item->getTitle();
            $results[$i]['start'] = date('c', strtotime($item->getDay()->format('d-m-Y')));
            $results[$i]['allDay'] = 'true';
            $results[$i]['end'] = $results[$i]['start'];

            ++$i;
        }

        return new JsonResponse($results);
    }

    public function getDataAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository(UserPersonal::class)->findById($id);

        $job = $em->getRepository(UserJob::class)->findByUserId($user);

        $family = $em->getRepository(UserFamily::class)->findByUserId($user);

        $results = [];

        if (0 == $user->getStatus()) {
            $results['nama'] = $user->getNama();
            $results['nama_registrasi'] = $user->getNoRegistrasi();
            $results['tempat-lahir'] = $user->getTempatLahir();
            $results['tanggal-lahir'] = $user->getTanggalLahir();
            $results['tanggal-pensiun'] = $user->getTanggalPensiun();
            $results['jenis-kelamin'] = $user->getJenisKelamin();
            $results['alamat-surat'] = $user->getAlamatSurat();
            $results['golongan-darah'] = $user->getGolonganDarah();
            $results['no-ktp'] = $user->getNoKtp();
            $results['agama'] = $user->getAgama();
            $results['kebangsaan'] = $user->getKebangsaan();
            $results['pendidikan'] = $user->getPendidikan();
            $results['asal-sekolah'] = $user->getAsalSekolah();
            $results['jurusan'] = $user->getJurusan();
            $results['bpjs'] = $user->getBpjs();
            $results['npwp'] = $user->getNpwp();
            $results['no-telp'] = $user->getNoTelp();
            $results['ukuran-pakaian'] = $user->getUkuranPakaian();
        } else {
            $results['golongan'] = $job->getGolongan();
            $results['jenjang-pangkat'] = $job->getJenjangPangkat();
            $results['tanggal-masuk'] = $job->getTanggalMasuk();
            $results['status-karyawan'] = $job->getStatusKaryawan();
            $results['pengalaman-kerja-terakhir'] = $job->getPengalamanKerjaTerakhir();
            $results['kontrak-training'] = $job->getKontrakTraining();
            $results['kontrak-kerja'] = $job->getKontrakKerja();
            $results['alamat-orang-tua'] = $family->getAlamatOrangTua();
        }

        return new JsonResponse($results);
    }

    public function testingAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(Attachment::class, 'u')->where('u.isValidated = 0');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['username'] = $item->getUserId()->getUsername();
            $results[$i]['description'] = $item->getDescription();

            ++$i;
        }

        return new JsonResponse($results);
//        return var_dump($data);
    }

    public function notifUserAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $em->getRepository(Attachment::class)->findBy(['userId' => $user]);

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['username'] = $item->getUserId()->getUsername();
            $results[$i]['description'] = $item->getDescription();

            ++$i;
        }

        return new JsonResponse($results);
    }

    public function notifValidatorAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $em->createQueryBuilder()
            ->select('attachment')
            ->from('OfficeBundle:UserPersonal', 'user')
            ->innerJoin('OfficeBundle:Attachment', 'attachment')
            ->where('user.id = attachment.userId');

        $i = 0;

        foreach ($data->getQuery()->getResult() as $item) {
            if ($item->getUserId()->getPenempatan() == $user->getPenempatan()) {
                $results[$i]['username'] = $item->getUserId()->getNama();
                $results[$i]['description'] = $item->getDescription();
                ++$i;
            }
        }

        return new JsonResponse($results);

//        return var_dump($user->getPenempatan()->getId());
    }

    public function listApiShiftAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(Shift::class, 'u');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['label'] = $item->getLabel();
            $results[$i]['start_time'] = $item->getStartTime();
            $results[$i]['end_time'] = $item->getEndTime();
            $results[$i]['created_at'] = $item->getCreatedAt();
            $results[$i]['updated_at'] = $item->getUpdatedAt();
            $results[$i]['office'] = $item->getOffice();

            ++$i;
        }

        return new JsonResponse($results);
    }

    public function listApiUserPresenceAction()
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(UserPresence::class, 'u');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['user_id'] = $item->getUserId()->getId();
            $results[$i]['shift_id'] = $item->getShift()->getId();
            $results[$i]['day'] = $item->getDay();
            $results[$i]['month'] = $item->getMonth();
            $results[$i]['year'] = $item->getYear();
            $results[$i]['data'] = $item->getData();
            $results[$i]['state'] = $item->getState();
            $results[$i]['created_at'] = $item->getCreatedAt();
            $results[$i]['description'] = $item->getDescription();

            $i++;
        }

        return new JsonResponse($results);
    }

    public function listApiCompanyProfileAction()
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(CompanyProfile::class, 'u');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['nama_perusahaan'] = $item->getNamaPerusahaan();
            $results[$i]['is_deleted'] = $item->getIsDeleted();

            $i++;
        }

        return new JsonResponse($results);
    }

    public function listApiHolidayAction()
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(Holiday::class, 'u');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['input_by'] = $item->getInputBy()->getId();
            $results[$i]['day'] = $item->getDay();
            $results[$i]['title'] = $item->getTitle();
            $results[$i]['created_at'] = $item->getCreatedAt();
            $results[$i]['updated_at'] = $item->getUpdatedAt();
            $results[$i]['days'] = $item->getDays();
            $results[$i]['month'] = $item->getMonth();
            $results[$i]['year'] = $item->getYear();

            $i++;
        }

        return new JsonResponse($results);
    }

    public function listApiAttachmentAction()
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(Attachment::class, 'u');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['user_id'] = $item->getUserId()->getId();
            $results[$i]['type_form'] = $item->getTypeForm();
            $results[$i]['absen'] = $item->getAbsen();
            $results[$i]['description'] = $item->getDescription();
            $results[$i]['tgl_mulai'] = $item->getTglMulai();
            $results[$i]['tgl_akhir'] = $item->getTglAkhir();
            $results[$i]['is_validated'] = $item->getIsValidated();
            $results[$i]['created_at'] = $item->getCreatedAt();
            $results[$i]['hash_date'] = $item->getHashDate();

            $i++;
        }

        return new JsonResponse($results);
    }

    public function listApiDeviceAction()
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(Device::class, 'u');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['user_id'] = $item->getDeviceName();
            $results[$i]['sn'] = $item->getSn();
            $results[$i]['ac'] = $item->getAc();
            $results[$i]['vc'] = $item->getVc();
            $results[$i]['vkey'] = $item->getVkey();

            $i++;
        }

        return new JsonResponse($results);
    }

    public function listApiCutiAction()
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(Device::class, 'u');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['user_id'] = $item->getUserId()->getId();
            $results[$i]['tanggal'] = $item->getTanggal();
            $results[$i]['bulan'] = $item->getBulan();
            $results[$i]['tahun'] = $item->getTahun();
            $results[$i]['is_validated'] = $item->getIsValidated();
            $results[$i]['validated_by'] = $item->getValidatedBy()->getId();
            $results[$i]['abs_date'] = $item->getAbsDate();
            $results[$i]['type_id'] = $item->getTypeId();
            $results[$i]['description'] = $item->getDescription();
            $results[$i]['hash_date'] = $item->getHashDate();
            $results[$i]['day_group'] = $item->getDayGroup();

            $i++;
        }

        return new JsonResponse($results);
    }

    public function listApiDayTypeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(DayType::class, 'u');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['data'] = $item->getData();
            $results[$i]['name'] = $item->getName();
            $results[$i]['count'] = $item->getCount();
            $results[$i]['is_deleted'] = $item->isDeleted();

            $i++;
        }

        return new JsonResponse($results);
    }

    public function listApiFingerprintAction()
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')->from(Fingerprint::class, 'u');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['user_id'] = $item->getUserId()->getId();
            $results[$i]['finger_status'] = $item->getFingerStatus();
            $results[$i]['finger_data'] = $item->getFingerData();
            $results[$i]['finger_salt'] = $item->getFingerSalt();

            $i++;
        }

        return new JsonResponse($results);
    }
}
