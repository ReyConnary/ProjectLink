package com.example.demospringboot.service;
// PekerjaService.java
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.example.demospringboot.entity.Interview;
import com.example.demospringboot.entity.Pekerja;
import com.example.demospringboot.entity.UndanganInterview;
import com.example.demospringboot.repository.UndanganInterviewRepository;
import com.example.demospringboot.repository.InterviewRepository;
import com.example.demospringboot.repository.PekerjaRepository;
import java.util.List;



@Service
public class PekerjaService {

@Autowired
private PekerjaRepository pekerjaRepository;
@Autowired
    private UndanganInterviewRepository undanganInterviewRepository;
    @Autowired
    private InterviewRepository interviewRepository;



public List<Pekerja> getAllEmps() {
return pekerjaRepository.findAll();
}
public Pekerja getEmpById(int id) {
return pekerjaRepository.findById(id).orElse(null);
}
public Pekerja addEmp(Pekerja emp) {
return pekerjaRepository.save(emp);
}
public Pekerja updateEmp(int id, Pekerja emp) {
emp.setIDPekerja(id);
return pekerjaRepository.save(emp);
}
public void deleteEmp(int id) {
pekerjaRepository.deleteById(id);
}
public UndanganInterview addUndanganInterview(UndanganInterview undanganInterview) {
        return undanganInterviewRepository.save(undanganInterview);
    }

    public Interview saveInterview(Interview interview) {
        return interviewRepository.save(interview);
    }


    public List<UndanganInterview> getAllUndanganInterviews() {
        return undanganInterviewRepository.findAll();
    }
    
    public List<Interview> getAllInterviews() {
        return interviewRepository.findAll();
    }

}
