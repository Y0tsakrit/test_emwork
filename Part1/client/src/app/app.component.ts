import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule, HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule],
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  showAdd: boolean = false;
  showEdit: boolean = false;
  showDelete: boolean = false;
  searchText: string = '';
  startDateFilter: string = '';
  endDateFilter: string = '';
  leaves: any[] = [];
  selectedLeaveId: number | null = null;

  newLeave: any = {
    name: '',
    department: '',
    email: '',
    phone: '',
    type: '',
    reason: '',
    startDate: '',
    endDate: ''
  };

  filteredLeavesResult: any[] = [];

  constructor(private http: HttpClient) {
    this.fetchLeaves();
  }

  get filteredLeaves() {
    const filtered = this.filteredLeavesResult.length
      ? this.filteredLeavesResult
      : this.leaves.filter(leave =>
          leave.name?.toLowerCase().includes(this.searchText.toLowerCase()) ||
          leave.type?.toLowerCase().includes(this.searchText.toLowerCase())
        );
    return filtered;
  }

  ADD() {
    this.showAdd = !this.showAdd;
  }

  showLeave(id: number) {
    this.selectedLeaveId = id;
    this.showDelete = true;
  }

  editLeave(leave: any) {
    this.showEdit = true;
    this.selectedLeaveId = leave.id;
  }

  deleteLeave() {
    if (!this.selectedLeaveId) return;

    const apiUrl = 'http://localhost:3000/api/delete';
    this.http.delete(apiUrl, { body: { id: this.selectedLeaveId } }).subscribe(
      (response: any) => {
        this.showDelete = false;
        this.selectedLeaveId = null;
        this.fetchLeaves();
        alert(response.message || 'Leave request deleted successfully!');
      },
      (error) => {
        alert(`Error deleting leave: ${error.error?.message || 'An unexpected error occurred.'}`);
      }
    );
  }

  saveLeave() {
    const apiUrl = 'http://localhost:3000/api/save';
    this.http.post(apiUrl, this.newLeave).subscribe(
      (response: any) => {
        this.showAdd = false;
        this.newLeave = {};
        this.fetchLeaves();
        alert(response.message || 'Leave request submitted successfully!');
      },
      (error) => {
        alert(`Error saving leave: ${error.error?.message}`);
      }
    );
  }

  fetchLeaves() {
    const apiUrl = 'http://localhost:3000/api/leaves';
    this.http.get<any[]>(apiUrl).subscribe(
      (response) => {
        this.leaves = response.map(leave => ({
          id: leave.id,
          name: leave.full_name,
          department: leave.position,
          email: leave.email,
          phone: leave.phone,
          type: leave.leave_type,
          reason: leave.reason,
          startDate: leave.start_date,
          endDate: leave.end_date,
          createdAt: leave.created_at,
          status: leave.status
        }));
      },
      (error) => {
        alert('Error fetching leaves.');
      }
    );
  }

  disLeave() {
    this.showDelete = false;
  }

  approveLeave(status: string) {
    if (!this.selectedLeaveId) return;

    const apiUrl = 'http://localhost:3000/api/change';
    this.http.patch(apiUrl, { id: this.selectedLeaveId, status }).subscribe(
      (response: any) => {
        this.showEdit = false;
        this.selectedLeaveId = null;
        this.fetchLeaves();
        alert(response.message || 'Leave status updated successfully!');
      },
      (error) => {
        alert(`Error updating leave status: ${error.error?.message}`);
      }
    );
  }

  filterByDate() {
    const startDate = new Date(this.startDateFilter);
    const endDate = new Date(this.endDateFilter);

    this.filteredLeavesResult = this.leaves.filter(leave => {
      const leaveStartDate = new Date(leave.startDate);
      const leaveEndDate = new Date(leave.endDate);
      return (
        (!this.startDateFilter || leaveStartDate >= startDate) &&
        (!this.endDateFilter || leaveEndDate <= endDate)
      );
    });
  }

  sortLeavesByDate() {
    this.filteredLeavesResult = this.filteredLeavesResult.length
      ? this.filteredLeavesResult.sort((a, b) => new Date(a.startDate).getTime() - new Date(b.startDate).getTime())
      : this.leaves.sort((a, b) => new Date(a.startDate).getTime() - new Date(b.startDate).getTime());
  }
}