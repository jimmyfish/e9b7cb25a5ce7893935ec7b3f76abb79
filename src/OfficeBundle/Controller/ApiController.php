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
            $results[$i]['id'] = $item->getId();
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
            $results[$i]['id'] = $item->getId();
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
            $results[$i]['id'] = $item->getId();
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
            $results[$i]['id'] = $item->getId();
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
            $results[$i]['id'] = $item->getId();
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
            $results[$i]['id'] = $item->getId();
            $results[$i]['device_name'] = $item->getDeviceName();
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

        $qb->select('u')->from(Cuti::class, 'u');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['id'] = $item->getId();
            $results[$i]['user_id'] = $item->getUserId()->getId();
            $results[$i]['tanggal'] = $item->getTanggal();
            $results[$i]['bulan'] = $item->getBulan();
            $results[$i]['tahun'] = $item->getTahun();
            $results[$i]['is_validated'] = $item->getIsValidated();
            $results[$i]['validated_by'] = $item->getValidatedBy() ? $item->getValidatedBy()->getId() : null;
            $results[$i]['abs_date'] = $item->getAbsDate();
            $results[$i]['type_id'] = $item ? $item->getType() : null;
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
            $results[$i]['id'] = $item->getId();
            $results[$i]['data'] = $item->getData();
            $results[$i]['name'] = $item->getName();
            $results[$i]['count'] = $item->getCount();
            $results[$i]['is_deleted'] = $item->getIsDeleted();

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
            $results[$i]['id'] = $item->getId();
            $results[$i]['user_id'] = $item->getUserId()->getId();
            $results[$i]['finger_status'] = $item->getFingerStatus();
            $results[$i]['finger_data'] = $item->getFingerData();
            $results[$i]['finger_salt'] = $item->getFingerSalt();

            $i++;
        }

        return new JsonResponse($results);
    }

    public function listApiUserFamilyAction()
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('u')
            ->from('OfficeBundle:UserPersonal', 'u')
            ->innerJoin('OfficeBundle:UserJob','r')
            ->where('u.id = r.userId');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['id'] = $item->getId();
            $results[$i]['nik'] = $item->getNik();
            $results[$i]['no_registrasi'] = $item->getNoRegistrasi();
            $results[$i]['username'] = $item->getUsername();
            $results[$i]['name'] = $item->getNama();
            $results[$i]['email'] = $item->getEmail();
            $results[$i]['password'] = $item->getPassword();
            $results[$i]['tempat_lahir'] = $item->getTempatLahir();
            $results[$i]['tanggal_lahir'] = $item->getTanggalLahir();
            $results[$i]['tanggal_pensiun'] = $item->getTanggalPensiun();
            $results[$i]['jenis_kelamin'] = $item->getJenisKelamin();
            $results[$i]['tempat_tinggal'] = $item->getTempatTinggal();
            $results[$i]['alamat_surat'] = $item->getAlamatSurat();
            $results[$i]['golongan_darah'] = $item->getGolonganDarah();
            $results[$i]['no_ktp'] = $item->getNoKtp();
            $results[$i]['agama'] = $item->getAgama();
            $results[$i]['pendidikan'] = $item->getPendidikan();
            $results[$i]['asal_sekolah'] = $item->getAsalSekolah();
            $results[$i]['kebangsaan'] = $item->getKebangsaan();
            $results[$i]['jurusan'] = $item->getJurusan();
            $results[$i]['bpjs'] = $item->getBpjs();
            $results[$i]['npwp'] = $item->getNpwp();
            $results[$i]['no_telp'] = $item->getNoTelp();
            $results[$i]['ukuran_pakaian'] = $item->getUkuranPakaian();
            $results[$i]['profile_picture'] = $item->getProfilePicture();
            $results[$i]['status'] = $item->getStatus();
            $results[$i]['role'] = $item->getRole();
            $results[$i]['golongan'] = $item->getJob() ? $item->getJob()->getGolongan() : null;
            $results[$i]['jenjang_pangkat'] = $item->getJob() ? $item->getJob()->getJenjangPangkat() : null;
            $results[$i]['jabatan'] = $item->getJob() ? $item->getJob()->getJabatan() : null;
            $results[$i]['tanggal_masuk'] = $item->getJob() ? $item->getJob()->getTanggalMasuk() : null;
            $results[$i]['status_karyawan'] = $item->getJob() ? $item->getJob()->getStatusKaryawan() : null;
            $results[$i]['pengalaman_kerja_terakhir'] = $item->getJob() ? $item->getJob()->getPengalamanKerjaTerakhir() : null;
            $results[$i]['kontrak_training'] = $item->getJob() ? $item->getJob()->getKontrakTraining() : null;
            $results[$i]['kontrak_kerja'] = $item->getJob() ? $item->getJob()->getKontrakKerja() : null;
            $results[$i]['tanggal_percobaan'] = $item->getJob() ? $item->getJob()->getTanggalPercobaan() : null;
            $results[$i]['tanggal_sk_tetap'] = $item->getJob() ? $item->getJob()->getTanggalSkTetap() : null;
            $results[$i]['dayoff_quotas'] = $item->getJob() ? $item->getJob()->getQuotas() : null;
            $results[$i]['status_perkawinan'] = $item->getFamily() ? $item->getFamily()->getStatusPerkawinan() : null;
            $results[$i]['pasangan'] = $item->getFamily() ? $item->getFamily()->getPasangan() : null;
            $results[$i]['orang_tua'] = $item->getFamily() ? $item->getFamily()->getOrangTua() : null;
            $results[$i]['alamat_orang_tua'] = $item->getFamily() ? $item->getFamily()->getAlamatOrangTua() : null;
            $results[$i]['mertua'] = $item->getFamily() ? $item->getFamily()->getMertua() : null;
            $results[$i]['company_profile_id'] = $item->getPenempatan() ? $item->getPenempatan()->getId() : null;
            $results[$i]['fingerprint_id'] = $item->getFinger() ? $item->getFinger()->getId() : null;
            $results[$i]['shift_id'] = $item->getJob()->getShift() ? $item->getJob()->getShift()->getId() : null;
            $results[$i]['is_validated'] = $item->getIsValidated();
            $results[$i]['is_active'] = $item->getIsActive();
            
            $i++;
        }

        return new JsonResponse($results);
    }
}
