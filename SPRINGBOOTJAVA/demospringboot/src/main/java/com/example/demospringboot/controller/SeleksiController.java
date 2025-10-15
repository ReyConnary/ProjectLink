package com.example.demospringboot.controller;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;

import com.example.demospringboot.service.SeleksiService;
import org.springframework.beans.factory.annotation.Autowired;
import com.example.demospringboot.entity.Seleksi;
import org.springframework.ui.Model;

import java.util.List;


@Controller
public class SeleksiController {
    
    @Autowired
 private SeleksiService seleksiService;

 
 //supaya ada page seleksi
  @GetMapping("/seleksi")
 public String seleksiPage2(Model model){
 @SuppressWarnings("unused")
 List<Seleksi> SelList;
 model.addAttribute("SelList", seleksiService.getAllSel()); 
model.addAttribute("SelInfo", new Seleksi());
 return "seleksi.html";
 }


//  tampilin tabel seleksi
 @GetMapping("/Seleksi/{id}")
 public String SeleksiGetRec(Model model, @PathVariable("id") int id){
 @SuppressWarnings("unused")
 List<Seleksi> SelList;
 @SuppressWarnings("unused")
Seleksi SelRec;
 model.addAttribute("SelList", seleksiService.getAllSel());
 model.addAttribute("SelRec", seleksiService.getSelById(id)); 
return "seleksi.html";
 }


 //manajer submit data seleksi
 @PostMapping(value={"/Seleksi/submit/", "/Seleksi/submit/{id}"}, params={"add"})
public String SeleksiAdd(Model model, @ModelAttribute("SelInfo") Seleksi SelInfo) {

    
    @SuppressWarnings("unused")
    Seleksi Sel = seleksiService.addSel(SelInfo);

    
    model.addAttribute("SelList", seleksiService.getAllSel());

    // balik ke seleksi abis submit
    return "redirect:/seleksi";
}


}

