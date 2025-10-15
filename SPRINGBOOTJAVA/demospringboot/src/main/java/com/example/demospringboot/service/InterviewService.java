package com.example.demospringboot.service;

import com.example.demospringboot.entity.Interview;
import com.example.demospringboot.repository.InterviewRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.time.LocalDate;
import java.util.List;
import java.util.Map;

@Service
public class InterviewService {

    @Autowired
    private InterviewRepository interviewRepository;

    // Save a new interview
    public Interview saveInterview(Interview interview) {
        return interviewRepository.save(interview);
    }

    // Retrieve an interview by ID
    public Interview getInterviewById(int id) {
        return interviewRepository.findById(id).orElse(null);
    }

    // Update an interview
    public Interview updateInterview(int id, Map<String, String> params) {
        Interview interview = getInterviewById(id);
        if (interview != null) {
            interview.setCatatan_interview(params.get("catatanInterview"));
            interview.setHasil_interview(params.get("hasilInterview"));
            interview.setTanggal_interview(LocalDate.parse(params.get("tanggalInterview")));
            return interviewRepository.save(interview);
        }
        return null;
    }

    // Delete an interview by ID
    public void deleteInterview(int id) {
        interviewRepository.deleteById(id);
    }

    public List<Interview> getAllInterviews() {
        return interviewRepository.findAll(); // Relies on JpaRepository
    }
    
}
