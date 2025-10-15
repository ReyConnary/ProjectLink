package com.example.demospringboot.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import com.example.demospringboot.service.InterviewService;
import com.example.demospringboot.service.PekerjaService;

import org.springframework.beans.factory.annotation.Autowired;

import com.example.demospringboot.entity.Interview;
import com.example.demospringboot.entity.Pekerja;
import com.example.demospringboot.entity.UndanganInterview;
import com.example.demospringboot.repository.UndanganInterviewRepository;

import org.springframework.ui.Model;

import java.time.LocalDate;
import java.util.List;

@Controller
public class PekerjaController {
    
    @Autowired
    private PekerjaService pekerjaService;

     @Autowired
    private InterviewService interviewService;

    // Tampilkan daftar pekerja
    @GetMapping("/pekerja")
    public String pekerjaPage(Model model) {
        model.addAttribute("EmpList", pekerjaService.getAllEmps());
        model.addAttribute("EmpInfo", new Pekerja());
        return "pekerja.html";
    }

    // Tampilkan detail pekerja tertentu
    @GetMapping("/pekerja/{id}")
    public String pekerjaGetRec(Model model, @PathVariable("id") int id) {
        model.addAttribute("EmpList", pekerjaService.getAllEmps());
        model.addAttribute("EmpRec", pekerjaService.getEmpById(id));
        return "pekerja.html";
    }

    // tambah Pekerja
    @PostMapping(value = {"/pekerja/submit/", "/pekerja/submit/{id}"}, params = {"add"})
    public String pekerjaAdd(Model model, @ModelAttribute("EmpInfo") Pekerja EmpInfo, RedirectAttributes redirectAttributes) {
        Pekerja Emp = pekerjaService.addEmp(EmpInfo);
        redirectAttributes.addFlashAttribute("pekerja", Emp);
        return "redirect:/pekerja"; 
    }

    // Edit Pekerja
    @PostMapping(value = "/pekerja/submit/{id}", params = {"edit"})
    public String pekerjaEdit(Model model, @ModelAttribute("EmpInfo") Pekerja EmpInfo, @PathVariable("id") int id, RedirectAttributes redirectAttributes) {
        Pekerja Emp = pekerjaService.updateEmp(id, EmpInfo);
        redirectAttributes.addFlashAttribute("pekerja", Emp); 
        return "redirect:/pekerja"; 
    }

    // Delete Pekerja
    @PostMapping(value = "/pekerja/submit/{id}", params = {"delete"})
    public String pekerjaDelete(Model model, @ModelAttribute("EmpInfo") Pekerja EmpInfo, @PathVariable("id") int id, RedirectAttributes redirectAttributes) {
        pekerjaService.deleteEmp(id);
        return "redirect:/pekerja";
    }

    // Thank you page abis submit complaint
    @GetMapping("/pekerja/thank-you")
    public String thankYouPage(Model model, @ModelAttribute("complaint") String complaint) {
        model.addAttribute("complaint", complaint);
        return "thank-you";
    }

    // Submit complaint
    @PostMapping(value = "/pekerja/submit/complaint/{id}")
    public String pekerjaSubmitComplaint(@PathVariable("id") int id, @ModelAttribute("complaint") String complaint, RedirectAttributes redirectAttributes) {
        Pekerja pekerja = pekerjaService.getEmpById(id);

        if (pekerja != null) {
            pekerja.setComplaint(complaint);
            pekerja.komplain(complaint);
        }

        redirectAttributes.addFlashAttribute("complaint", complaint);
        redirectAttributes.addFlashAttribute("pekerja", pekerja);
        return "thank-you";
    }

    // Login utk pekerja bukan manajer(diatas itu manager)
    @GetMapping("/login")
    public String loginPage() {
        return "login";
    }

    // pekerja login
    @PostMapping("/pekerja/login")
    public String loginPekerja(@RequestParam("id") int id, @RequestParam("password") String password, RedirectAttributes redirectAttributes) {
        Pekerja pekerja = pekerjaService.getEmpById(id);

        //pake password yg di set di atas ke id tertentu bwt login

        if (pekerja != null && pekerja.getPassword().equals(password)) {
            redirectAttributes.addFlashAttribute("pekerja", pekerja);  // Persist Pekerja data for redirect
            return "redirect:/pekerja/singular";
        }

        return "redirect:/login";
    }

    // Halaman pekerja
    @GetMapping("/pekerja/singular")
    public String singularPekerjaPage(Model model) {
        return "singular-pekerja";
    }

    // Buat Undangan Interview
    @PostMapping("/pekerja/undanganinterview")
    public String createUndanganInterview(@RequestParam("lokasiInterview") String lokasiInterview, @RequestParam("tanggalInterview") String tanggalInterview, 
                                          @RequestParam("waktuInterview") String waktuInterview, @RequestParam("IDPekerja") int idPekerja, RedirectAttributes redirectAttributes) {
        UndanganInterview undanganInterview = new UndanganInterview();
        undanganInterview.setLokasiInterview(lokasiInterview);
        undanganInterview.setTanggalInterview(tanggalInterview);
        undanganInterview.setWaktuInterview(waktuInterview);
        undanganInterview.setIDPekerja(idPekerja);

        pekerjaService.addUndanganInterview(undanganInterview);
        return "redirect:/login";  // Abis buat, balik ke login
    }

    // Simpan data interview
    @PostMapping("/pekerja/interview")
    public String saveInterview(@RequestParam("IDPelamar") int IDPelamar, @RequestParam("IDPekerja") int IDPekerja, 
                                @RequestParam("catatanInterview") String catatanInterview, @RequestParam("hasilInterview") String hasilInterview, 
                                @RequestParam("tanggalInterview") String tanggalInterview) {

        Interview interview = new Interview();
        interview.setIDPelamar(IDPelamar);
        interview.setIDPekerja(IDPekerja);
        interview.setCatatan_interview(catatanInterview);
        interview.setHasil_interview(hasilInterview);
        interview.setTanggal_interview(LocalDate.parse(tanggalInterview));

        pekerjaService.saveInterview(interview);
        return "redirect:/login";
    }


    @Autowired
    private UndanganInterviewRepository undanganInterviewRepository;

    public List<UndanganInterview> getAllUndanganInterviews() {
        return undanganInterviewRepository.findAll();
    }


@GetMapping("/interviews")
public String showInterviewsPage(Model model) {
    // supaya klo masuk link localhost interviews akan keluar data undanganinterview, interview
    model.addAttribute("undanganInterviews", pekerjaService.getAllUndanganInterviews());

    List<Interview> interviews = interviewService.getAllInterviews();
    model.addAttribute("interviews", interviews);

    return "interviews";
}







    
}
