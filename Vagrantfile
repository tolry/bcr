Vagrant.configure('2') do |config|
    config.vm.box       = 'ubuntu/bionic64'
    config.vm.host_name = 'bcr.www'
    config.ssh.forward_agent = true

    config.vm.network 'private_network', ip: '192.168.123.12'
    config.vm.network 'forwarded_port', guest: 80, host: 9050

    config.vm.synced_folder ".", "/var/www", :nfs => true

    config.vm.provision :shell, path: "bootstrap.sh"

    config.vm.provision "shell" do |s|
        ssh_pub_key = File.readlines("#{Dir.home}/.ssh/id_rsa.pub").first.strip
        s.inline = <<-SHELL
          echo #{ssh_pub_key} >> /home/vagrant/.ssh/authorized_keys
          echo #{ssh_pub_key} >> /root/.ssh/authorized_keys
        SHELL
     end

    config.vm.provision :ansible do |ansible|
        ansible.playbook = "ansible/main.yml"
    end
end
