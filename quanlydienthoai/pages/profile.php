<div style="background: #ffffff; max-width: 500px; padding: 24px 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #eee; font-family: Arial, sans-serif; margin: 20px auto;">
    <h3 style="color: #333; font-size: 20px; font-weight: 700; margin-top: 0; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #D70018;">Thông tin tài khoản</h3>
    
    <div style="display: flex; flex-direction: column; gap: 14px;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dashed #eee;">
            <span style="color: #666; font-weight: 600; font-size: 14px;">Họ tên:</span>
            <span style="color: #222; font-weight: 600; font-size: 15px;"><?php echo $user['fullname'] ?? $user['ho_ten'] ?? 'Hoàng'; ?></span>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dashed #eee;">
            <span style="color: #666; font-weight: 600; font-size: 14px;">Email:</span>
            <span style="color: #222; font-weight: 500; font-size: 15px;"><?php echo $user['email'] ?? 'nguyenviethoang270306@gmail.com'; ?></span>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dashed #eee;">
            <span style="color: #666; font-weight: 600; font-size: 14px;">Số điện thoại:</span>
            <span style="color: #222; font-weight: 500; font-size: 15px;"><?php echo $user['phone'] ?? $user['sdt'] ?? '2383274623623'; ?></span>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dashed #eee;">
            <span style="color: #666; font-weight: 600; font-size: 14px;">Địa chỉ:</span>
            <span style="color: #222; font-weight: 500; font-size: 15px;"><?php echo $user['address'] ?? $user['dia_chi'] ?? 'hhhhh'; ?></span>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
            <span style="color: #666; font-weight: 600; font-size: 14px;">Quyền:</span>
            <span style="background: #e6f7ff; color: #1890ff; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: bold; text-transform: uppercase;"><?php echo $user['role'] ?? 'user'; ?></span>
        </div>
    </div>
</div>