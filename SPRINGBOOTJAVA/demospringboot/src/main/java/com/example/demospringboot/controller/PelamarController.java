package com.example.demospringboot.controller;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import com.example.demospringboot.service.PelamarService;
import org.springframework.beans.factory.annotation.Autowired;
import com.example.demospringboot.entity.Pelamar;
import org.springframework.ui.Model;
import java.util.List;


@Controller
public class PelamarController {
    
    @Autowired
 private PelamarService pelamarService;

 //supaya ada webpage pelamar
  @GetMapping("/pelamar")
 public String pelamarPage2(Model model){
 @SuppressWarnings("unused")
 List<Pelamar> PelList;
 model.addAttribute("PelList", pelamarService.getAllPel()); 
model.addAttribute("PelInfo", new Pelamar());
 return "pelamar.html";
 }

 @GetMapping("/pelamar/{id}")
 public String pelamarGetRec(Model model, @PathVariable("id") int id){
 @SuppressWarnings("unused")
 List<Pelamar> PelList;
 @SuppressWarnings("unused")
Pelamar PelRec;
 model.addAttribute("PelList", pelamarService.getAllPel());
 model.addAttribute("PelRec", pelamarService.getPelById(id)); 
return "pelamar.html";
 }



//add data pelamar
 @PostMapping( value={"/pelamar/submit/", "/pelamar/submit/{id}"}, params={"add"})
 public String pelamarAdd(Model model, 
 @ModelAttribute("PelInfo") Pelamar PelInfo)
 {
 @SuppressWarnings("unused")
 Pelamar Pel = pelamarService.addPel(PelInfo);
 @SuppressWarnings("unused")
 List<Pelamar> PelList;
 model.addAttribute("PelList", pelamarService.getAllPel());
 return "redirect:/pelamar/thank-you";
 }


//ke page thank you jika selesai isi data
 @GetMapping("/pelamar/thank-you")
public String thankYouPage(Model model, @ModelAttribute("complaint") String complaint) {
    model.addAttribute("complaint", complaint);
    return "thank-you";
}


//jika selesai submit complaint maka akan ke thank you page dengan komplain nya tertulis
@PostMapping(value = "/pelamar/submit/complaint/{id}")
public String pelamarSubmitComplaint(
        @PathVariable("id") int id,
        @ModelAttribute("complaint") String complaint,
        RedirectAttributes redirectAttributes) {
    Pelamar pelamar = pelamarService.getPelById(id);

    if (pelamar != null) {
        pelamar.setComplaint(complaint);

        pelamar.komplain(complaint);
    }

    redirectAttributes.addFlashAttribute("complaint", complaint);

    return "redirect:/pelamar/thank-you";
}


//ini buat pelamar data viewer buat dilihat manajer
@GetMapping("/pelamardata")
public String pelamarPage(Model model) {
    List<Pelamar> pelList = pelamarService.getAllPel();
    model.addAttribute("PelList", pelList);
    return "pelamardata";
}


//ini buat pelamar data viewer buat dilihat pekerja
@GetMapping("/pelamardata2")
public String pelamarPage3(Model model) {
    List<Pelamar> pelList = pelamarService.getAllPel();
    model.addAttribute("PelList", pelList);
    return "pelamardata2"; // Matches the pelamar.html file
}


}

