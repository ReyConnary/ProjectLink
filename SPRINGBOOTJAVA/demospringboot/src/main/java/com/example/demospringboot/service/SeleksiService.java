package com.example.demospringboot.service;
// SeleksiService.java
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.example.demospringboot.entity.Seleksi;
import com.example.demospringboot.repository.SeleksiRepository;
import java.util.List;



@Service
public class SeleksiService {
@Autowired
private SeleksiRepository seleksiRepository;
public List<Seleksi> getAllSel() {
return seleksiRepository.findAll();
}
public Seleksi getSelById(int id) {
return seleksiRepository.findById(id).orElse(null);
}
public Seleksi addSel(Seleksi pel) {
return seleksiRepository.save(pel);
}
public Seleksi updateSel(int id, Seleksi sel) {
sel.setKodeSeleksi(id);
return seleksiRepository.save(sel);
}
public void deleteSel(int id) {
seleksiRepository.deleteById(id);
}
}
