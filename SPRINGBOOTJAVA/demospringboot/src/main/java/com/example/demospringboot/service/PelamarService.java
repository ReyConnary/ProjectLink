package com.example.demospringboot.service;
// PelamarService.java
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.example.demospringboot.entity.Pelamar;
import com.example.demospringboot.repository.PelamarRepository;
import java.util.List;



@Service
public class PelamarService {
@Autowired
private PelamarRepository pelamarRepository;
public List<Pelamar> getAllPel() {
return pelamarRepository.findAll();
}
public Pelamar getPelById(int id) {
return pelamarRepository.findById(id).orElse(null);
}
public Pelamar addPel(Pelamar pel) {
return pelamarRepository.save(pel);
}
public Pelamar updatePel(int id, Pelamar pel) {
pel.setIDPelamar(id);
return pelamarRepository.save(pel);
}
public void deletePel(int id) {
pelamarRepository.deleteById(id);
}
}
